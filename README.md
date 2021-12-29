# Tatter\Permits
Model permission handling for CodeIgniter 4

[![](https://github.com/tattersoftware/codeigniter4-permits/workflows/PHPUnit/badge.svg)](https://github.com/tattersoftware/codeigniter4-permits/actions/workflows/test.yml)
[![](https://github.com/tattersoftware/codeigniter4-permits/workflows/PHPStan/badge.svg)](https://github.com/tattersoftware/codeigniter4-permits/actions/workflows/analyze.yml)
[![](https://github.com/tattersoftware/codeigniter4-permits/workflows/Deptrac/badge.svg)](https://github.com/tattersoftware/codeigniter4-permits/actions/workflows/inspect.yml)
[![Coverage Status](https://coveralls.io/repos/github/tattersoftware/codeigniter4-permits/badge.svg?branch=develop)](https://coveralls.io/github/tattersoftware/codeigniter4-permits?branch=develop)

## Quick Start

1. Install with Composer: `> composer require tatter/permits`
2. Add the trait to your models:
```php
class BlogModel extends Model
{
    use PermitsTrait;
```
3. Use the CRUDL verbs to check access: `if ($blogs->mayCreate()) ...`

## Features

`Permits` solves a common problem with object rights management: "Can this user
add/change/remove this item?" This library provides object-level access rights to your
Model classes via a single trait that adds CRUDL-style verbs to your model.

## Installation

Install easily via Composer to take advantage of CodeIgniter 4's autoloading capabilities
and always be up-to-date:
```bash
composer require tatter/permits
```

Or, install manually by downloading the source files and adding the directory to
**app/Config/Autoload.php**.

`Permits` requires the Composer provision for `codeigniter4/authentication-implementation`
as describe in the [CodeIgniter authentication guidelines](https://codeigniter4.github.io/CodeIgniter4/extending/authentication.html).
You must install and configure a [supported package](https://packagist.org/providers/codeigniter4/authentication-implementation)
to handle authentication and authorization in order for `Permits` to understand your users.

## Configuration (optional)

The library's default behavior can be altered by extending its config file. Copy
**examples/Permits.php to **app/Config/** and follow the instructions in the comments.
If no config file is found in **app/Config** the library will use its own.

The Config files includes a set of `$default` access levels which you may modify in your
own version. Each Model you intend to use should have a Config property corresponding to
its `$table` property with properties for anything that needs adjusting from the defaults.

Once your configuration is complete simply include the `PermitsTrait` on your models to
enable the methods:
```php
use CodeIgniter\Model;
use Tatter\Permits\Traits\PermitsTrait;

class FruitModel extends Model
{
    use PermitsTrait;
...
```

## Usage

There are two types of permissions: explicit and inferred. Both rely on this common set of
CRUDL verbs:
* **create**: Make new items
* **read**: View a single item
* **update**: Make changes to a single item
* **delete**: Delete a single item
* **list**: View an index of all items
* **admin**: Perform any of the above regardless of other rights

Use the corresponding Model verbs to check the access:
```php
if (! $model->mayCreate()) {
    return redirect()->back()->with('error', 'You do not have permission to do that!');
}

$item = $model->find($id);
if (! $model->mayUpdate($item)) {
    return redirect()->back()->with('error', 'You can only update your own items!');
}
```

`PermitsTrait` will check access rights based on the current logged in user (if there is one)
but you may also pass an explicit user ID to check instead:
```php
if (! $model->mayAdmin($userId)) {
    log_message('debug', "User #{$userId} attempted to access item administration.");
}
```

### Explicit 

Explicit permissions are granted to users or groups via your authorization library. `Permits`
uses [Tatter\Users](https://packagist.org/packages/tatter/users) to interact with user records
and determine explicit permissions. If your authentication package is not supported by
`Users` autodiscovery then be sure to [read the docs](https://github.com/tattersoftware/codeigniter4-users)
on how to include it.

When checking explicit permissions `Permits` uses the format "table.verb". For example, if
your project includes a `BlogModel` and you want to allow Blog Editors access to edit anybody's
blog entries you would assign "blogs.edit" to the "editors" group.

> Note: Explicit permissions always take precedence over inferred permissions.

### Inferred

Inferred permissions use the configuration (see above) to determine any individual user's
access to an item or group of items. There are four access levels which may be applied to
each verb (these are constants on `Tatter\Permits\Config\Permits`):

* `NOBODY`: Prohibits all access without explicit permission
* `OWNERS`: Requires the authenticated user to own the item
* `OWNERS`: Requires the authenticated user to own the item
* `USERS`: Requires any authenticated user
* `ANYBODY`: Allows anyone regardless of authentication or ownership

In addition to the access levels, each model should be configured on how to determine item
ownership. Set whichever of the following values are necessary on your table's Config property:
 * `userKey`: Field for the user ID in the item or its pivot table
 * `pivotKey`: Field for the item's ID in the pivot tables
 * `pivotTable`: Table that joins the items to their owners

## Example

Your web app includes a Content Management System that allows the site owners to log in and
update various parts of the site. This includes a blog section, with its own Model, Controller,
and Views. Any visitors to your site can create an account and submit a blog entry, but it
needs to be approved before it "goes live". Being the brilliant developer you are, you decide
to use `Tatter\Permits` to manage access to the blog entries.

> `Permits` requires an [authentication implementation](https://packagist.org/providers/codeigniter4/authentication-implementation);
> for this example we will use [Shield](https://github.com/lonnieezell/codeigniter-shield).

First we need to make sure our authentication package is ready with the correct permissions.
This may vary back package, but `Shield` defines Groups and Permissions using
[Config files](https://github.com/lonnieezell/codeigniter-shield/blob/develop/docs/3%20-%20authorization.md).
We will leave the existing groups and add a new "editors" group.
**app/Config/AuthGroups.php**:
```php
    public $groups = [
        'superadmin' => [
            'title'       => 'Super Admin',
            'description' => 'Complete control of the site.',
        ],
        'admin' => [
            'title'       => 'Admin',
            'description' => 'Day to day administrators of the site.',
        ],
        'developer' => [
            'title'       => 'Developer',
            'description' => 'Site programmers.',
        ],
        'user' => [
            'title'       => 'User',
            'description' => 'General users of the site. Often customers.',
        ],
        'beta' => [
            'title'       => 'Beta User',
            'description' => 'Has access to beta-level features.',
        ],
        'editor' => [
            'title'       => 'Blog Editors',
            'description' => 'Has access to all blog entries.',
        ],
    ];
```

We want to give explicit permission for blog administration to some groups, so in the same
file we add a new permission in the format "{table}.{verb}":
```php
    public $permissions = [
        'admin.access'        => 'Can access the sites admin area',
        'admin.settings'      => 'Can access the main site settings',
        'users.manage-admins' => 'Can manage other admins',
        'users.create'        => 'Can create new non-admin users',
        'users.edit'          => 'Can edit existing non-admin users',
        'users.delete'        => 'Can delete existing non-admin users',
        'beta.access'         => 'Can access beta-level features',
        'blogs.admin'         => 'Allows all access to blog model operations',
    ];
```

Finally we add the new permission to the groups we want to have it, in the same file still:
```php
    public $matrix = [
        'superadmin' => [
            'admin.*',
            'users.*',
            'beta.*',
            'blogs.*',
        ],
        'admin' => [
            'admin.access',
            'users.create',
            'users.edit',
            'users.delete',
            'beta.access',
            'blogs.admin',
        ],
        'developer' => [
            'admin.access',
            'admin.settings',
            'users.create',
            'users.edit',
            'beta.access',
        ],
        'user' => [],
        'beta' => [
            'beta.access',
        ],
        'editor' => [
            'blogs.admin',
        ],
    ];
```

That's it for the third-party authorization configuration! On to `Permits` - first thing we
need is to set the permissions in our Config file. We can leave the defaults as they are
and add our own property.
**app/Config/Permits.php**:
```php
    /*
     * @var array<string,mixed>
     */
    public $blogs = [
        'admin'      => self::NOBODY,
        'create'     => self::USERS,
        'list'       => self::USERS,
        'read'       => self::OWNERS,
        'update'     => self::OWNERS,
        'delete'     => self::OWNERS,
        'userKey'    => 'user_id',
        'pivotKey'   => null,
        'pivotTable' => null,
    ];
}
```

Let's break that down.

1. The first permission, "admin": we gave explicit rights to above in our
auth package so we do not want anyone else having access, hence `NOBODY`. Explicit permissions
take precedence so our "superadmin", "admin", and "editor" groups will still have full access.

2. Next are "list" and "create": both are available to `USERS` - that is, anyone who is logged
in. They will be able to create new entries and see a list of others' entries.

3. However, "read", "update", and "delete" are all restricted to `OWNERS` - authenticated users will only
be able to click on their own entries to read and modify the content.

4. Finally, we need a way for `Permits` to decide "who owns this". In this case we set "userKey"
but leave the pivot properties blank - meaning, our `blogs` table has a field called `user_id`
which corresponds to the ID of the user that created the blog.

> In more complex setups where multiple users are assigned to multiple blogs we might have a
> join table, in which case we would also have set "pivotTable" to something like `blogs_users`
> and "pivotKey" like `blog_id`.

Configuration complete! The final piece to the integration is to add our trait to the blog
model, which will handle activating our access verbs.
**app/Models/BlogModel.php**:
```php

use App\Entities\Blog;
use CodeIgniter\Model;
use Tatter\Permits\Traits\PermitsTrait;

class BlogModel extends Model
{
    use PermitsTrait;

    protected $table      = 'blogs';
    protected $primaryKey = 'id';
    protected $returnType = Blog::class;
...
}
```

Integration complete! Now you are ready to start using `Permits` in your code. Let's make
a Controller for our blogs and add some permissions checks before the regular code.
**app/Controllers/Blogs.php**:
```php
<?php

namespace App\Controllers;

use App\Models\BlogModel;
use CodeIgniter\HTTP\RedirectResponse;

class Blogs extends BaseController
{
    /**
     * @var BlogModel
     */
    protected $model;

    /**
     * Preloads the model.
     */
    public function __construct()
    {
        $this->model = model(BlogModel::class);
    }

    /**
     * Displays the list of approved blogs
     * for all visitors of the website.
     */
    public function index(): string
    {
        return view('blogs/public', [
            'blogs' => $this->model->findAll(),
        ]);
    }
    
    /**
     * Displays blogs eligible for updating
     * based on the authenticated user (handled
     * by our authentication Filter).
     */
    public function manage(): string
    {
        // Admin access sees all blogs, otherwise limit to the current user
        if (! $this->model->mayAdmin()) {
            $this->model->where('user_id', user_id());
        }

        return view('blogs/manage', [
            'blogs' => $this->model->findAll(),
        ]);
    }
    
    /**
     * Shows a single blog with options
     * to update or delete.
     *
     * @return RedirectResponse|string
     */
    public function edit($blogId)
    {
        // Verify the blog
        if (empty($blogId) || null === $blog = $this->model->find($blogId)) {
            return redirect()->back()->with('error', 'Could not find that blog entry.');
        }

        // Check access
        if (! $this->model->mayUpdate($blog)) {
            return redirect()->back()->with('error', 'You do not have permission to do that.');
        }

        return view('blogs/edit', [
            'blog' => $blog,
        ]);
    }
...
```

Hopefully you get the idea from here! For developers who like to keep their controllers
even more lightweight you could even put some of these checks into a Filter.

## Extending

The CRUDL-style methods are just a starting point! Your models can override these built-in
methods or add new methods that take advantage of the library's structure and methods.
Check out the code in the source repo for ideas how to leverage both explicit and inferred
permissions.
