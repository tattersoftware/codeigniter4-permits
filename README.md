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
class JobModel extends Model
{
	use PermitsTrait;
```
3. Use the CRUDL verbs to check access: `if ($jobs->mayCreate()) ...`

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

**Coming soon**
