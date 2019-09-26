# Tatter\Permits
Lightweight permission handler for CodeIgniter 4

## Quick Start

1. Install with Composer: `> composer require tatter/permits`
2. Update the database: `> php spark migrate -all`
3. Extend your models: `class JobModel extends Tatter\Permits\Models\PModel`;
4. Ready to use! `if ($jobs->mayCreate()) ...`
5. (Optional) Add overrides:
`php spark permits:add`
or
`php spark permits:add deleteJobs groups 7`

## Features

Provides out-of-the-box object permissions for CodeIgniter 4

## Installation

Install easily via Composer to take advantage of CodeIgniter 4's autoloading capabilities
and always be up-to-date:
* `> composer require tatter/permits`

Or, install manually by downloading the source files and adding the directory to
`app/Config/Autoload.php`.

Once the files are downloaded and included in the autoload, run any library migrations
to ensure the database is setup correctly:
* `> php spark migrate -all`

## Configuration (optional)

The library's default behavior can be altered by extending its config file. Copy
**bin/Permits.php to **app/Config/** and follow the instructions in the comments.
If no config file is found in app/Config the library will use its own.

## Usage

There are two types of permissions: explicit and inferred.

### Explicit 

Explicit permissions are granted to users or groups via database entries in the `permits`
table. Entries might look like:

| User ID | Group ID | Permit     |
| ------- | -------- | ---------- |
|       7 |          | readUsers  |
|         |        3 | deleteJobs |

You can check if a user has a specific permit, or inherits it from one of their groups:

* `service('permits')->hasPermit($userId, 'readUsers');`

The library also comes with convenient CLI commands for managing explicit permissions;
run `> php spark` for a list of commands in the "Permits" group.

### Inferred

Inferred permissions are handle by your models in a `chmod`-style four-digit octal mode. By
default the Permits Model comes with mode `04664`, or "domain list, owner write, group write,
world read":
* 4 Domain list, no create
* 6 Owner  read, write
* 6 Group  read, write
* 4 World  read, no write

You may (and should) set your own mode on your models by providing an octal (0####) value
to the `$mode` property. **Hint:** Think of the first digit as permission to the "directory",
controlling access to list and create new files, and the remaining three digits as the "files",
controlling access to each individual instance of your model's return-type.

In addition to the mode, you may supply your model with database information on how to
determine user and group ownership. Consider the following variables:
```
// name of the user ID in this model's objects
protected $userKey;

// name of the group ID in this model's objects
protected $groupKey;

// name of this object's ID in the pivot tables
protected $pivotKey;

// table that joins this model's objects to its users
protected $usersPivot;

// table that joins this model's objects to its groups
protected $groupsPivot;
```

Each model expects either an ID field or a pivot table for both users and groups to
determine if a particular object is accessible. A simple example might help.
```
// app/Models/JobModel.php
class JobModel extends Tatter\Permits\Model
{
	protected $table      = 'jobs';
	protected $primaryKey = 'id';
	protected $returnType = 'App\Entities\Job';
	
	...
		
	// Permits
	public $mode       = 04660;
	public $groupKey   = 'group_id';
	public $pivotKey   = 'job_id';
	public $usersPivot = 'jobs_users';
}
```
This creates a new permitted model `JobModel`, with `$mode` `04660`, so any user may list
jobs but would need an explicit permit to create a new one. Users and groups have full
access to any job they have ownership of, but cannot even view details on other jobs.
For ownership, `JobModel` tells the library to check the `Job` entity for a key
`group_id` to determine which group has ownership. `JobModel` also defines
`jobs_users` (`job_id`,`user_id`) as a source for users who have ownership, so multiple
individuals may be assigned to the same job without being in its ownership group.

Once your models and entities are setup, you are ready to use the built-in commands
(or add your own) to check user permissions:
```
$jobs = new JobModel();
if ($jobs->mayCreate())
{
	...
}

$job = $jobs->find($jobId);
if ($jobs->mayUpdate($job))
{
	...
}
```
Built-in commands are CRUD-style: `mayCreate()`, `mayRead($object)`, `mayUpdate($object)`,
`mayDelete($id)`, `mayList()`, `mayAdmin()`. Command parameters are the object to test (for
methods that work on specific instances) and an optional `$userId` to specify who to test
for permissions. Omitting `$userId` will default to the current logged-in user, as
configured in **Config/Permits.php**.

The super-permit `mayAdmin()` checks for the explicit permit `admin{Table}` and will
supercede any of the other built-in commands.

## Extending

Permits comes with a basic user model for reading and testing authorizations,
but in most cases you will want to provide your own. You may extend the built-in model
`\Tatter\Permits\Models\User` or supply your own to `Services::permits()`, or directly
to the class. If you use your own model make sure it implements the Permits User Model
interface (`\Tatter\Permits\Interfaces\PermitUserModelInterface`) and has an appropriate
`groups()` method.

The CRUDL-style methods are just a starting point! Your models can override these built-in
methods or add new methods that take advantage of the library's structure and methods.
Check out the code in the examples for ideas how to leverageboth explicit and inferred
permissions.

### PermitsTrait

If you cannot extend the model (for example, already extending another library's model)
you can supply the necessary class variables directly (see above) and use the library's
trait to access the class "may" methods:
```
class MyModel extends \Tatter\Relations\Model
{
	use \Tatter\Permits\Traits\PermitsTrait;
	
	public $mode     = 06666;
	public $pivotKey = 'foo_id';
	...
```
