# Upgrade Guide

## Version 2 to 3
***

* Now requires a verified authentication system via `codeigniter4/authentication-implementation`
* Switches to `Tatter\Users` for interface handling; [read the docs](https://github.com/tattersoftware/codeigniter4-users) to be sure your Models and Entities are configured
* Fixes a function name discrepancy in the helper - be sure to use `mode2array()` not `octal2array()`
