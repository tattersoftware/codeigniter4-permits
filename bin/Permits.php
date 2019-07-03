<?php namespace Config;

/***
*
* This file contains example values to alter default library behavior.
* Recommended usage:
*	1. Copy the file to app/Config/Permits.php
*	2. Change any values
*	3. Remove any lines to fallback to defaults
*
***/

use CodeIgniter\Config\BaseConfig;

class Permits extends \Tatter\Permits\Config\Permits
{
	// key in $_SESSION that contains the integer ID of a logged in user
	public $sessionUserId = "userId";
	
	// whether to implement groups access across the library
	// set to 'false' if you don't have a groups table implemented
	public $useGroups = true;
	
	// number of seconds to cache a permission
	public $cacheDuration = 60;
	
	// whether to continue instead of throwing exceptions
	public $silent = true;
}
