# Upgrade Guide

## Version 2 to 3
***

> Note: This is a complete refactor! Please be sure to read the docs carefully before upgrading.

* Minimum PHP version has been bumped to `7.4` to match the upcoming framework changes
* All properties that can be typed have been
* Now requires a verified authentication system via `codeigniter4/authentication-implementation`
* Switches to `Tatter\Users` for interface handling; [read the docs](https://github.com/tattersoftware/codeigniter4-users) to be sure your Models and Entities are configured
* No longer handles explicit permissions - these should now handled by your Auth library; read more below
* Related, the follow classes have been removed: `PermitModel`, `UserModel`, `PermitsFilter`, `PermitException`, and the Permits commands, language, and migration files
* Permissions are now set in the Config file with friendly strings, so the **chmod Helper** is no longer necessary and has been removed

### Permissions

In order to reduce overlap this library no longer handles explicit permissions; instead
permissions should now handled by your Auth library. `Permits` uses the user interface
extension `Tatter\Users\Interfaces\HasPermission` to check for explicit permissions in the
format "{table}.{verb}". If you have existing explicit permissions (i.e. entries in the
`permits` table) then convert them to your Auth library's format before removing the
`permits` table. You may also need to update your `migrations` table in case your project
complains about a "gap in the migrations".

Likewise, the `PermitsFilter` has been removed because most Auth libraries now include the
same functionality. Update any references in **app/Config/Filters.php**.
