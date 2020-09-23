<?php namespace Tatter\Permits;

use Tatter\Permits\Traits\PermitsTrait;

class Model extends \CodeIgniter\Model
{
	use PermitsTrait;
	
	/* Default mode:
	 * 4 Domain list, no create
	 * 6 Owner  read, write
	 * 6 Group  read, write
	 * 4 World  read, no write
	 */
	protected $mode = 04664;
	
	// Name of the user ID in this model's objects
	protected $userKey;
	// Name of the group ID in this model's objects
	protected $groupKey;
	// Name of this object's ID in the pivot tables
	protected $pivotKey;
	
	// Table that joins this model's objects to its users
	protected $usersPivot;
	// Table that joins this model's objects to its groups
	protected $groupsPivot;
}
