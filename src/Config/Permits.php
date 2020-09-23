<?php namespace Tatter\Permits\Config;

use CodeIgniter\Config\BaseConfig;

class Permits extends BaseConfig
{
	// key in $_SESSION that contains the integer ID of a logged in user
	public $sessionUserId = 'logged_in';

	// whether to implement groups access across the library
	public $useGroups = true;

	// number of seconds to cache a permission
	public $cacheDuration = 60;

	// whether to continue instead of throwing exceptions
	public $silent = true;
}
