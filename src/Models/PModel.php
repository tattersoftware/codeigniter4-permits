<?php namespace Tatter\Permits\Models;

use CodeIgniter\Model;
use CodeIgniter\Config\Services;

class PModel extends Model
{
	protected $tableMode = 0664;
	protected $rowMode = 0664;
	
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
	
	// whether the current/supplied user may insert rows into this model's table
	public function mayCreate(int $userId = null): bool
	{		
		// load the library
		$permits = Services::permits();
		
		// if no user provided, check for a logged in user
		$userId = $userId ?? $permits->sessionUserId();

		// check for a permit
		if ($permit = $permits->hasPermit($userId, 'create' . ucfirst($this->table)))
			return true;
		
		// make sure permissions are setup correctly
		if (! is_octal($this->tableMode))
			return false;
			
		// check if the table itself is world-writeable
		if ($permissions = mode2array($this->tableMode))
			return $permissions['world']['write'];
		
		return false;
	}
	
	// whether the current/supplied user may read the given object
	public function mayRead($object, int $userId = null): bool
	{
		// load the library
		$permits = Services::permits();
		
		// if no user provided, check for a logged in user
		$userId = $userId ?? $permits->sessionUserId();

		// check for an explicit permit
		if ($permit = $permits->hasPermit($userId, 'read' . ucfirst($this->table)))
			return true;
		
		// make sure permissions are setup correctly
		if (! $permits->isPermissible($object, $this))
			return false;
		$permissions = mode2array($this->rowMode);
		
		// check if the object is world-readable
		if ($permissions['world']['read'])
			return true;
		
		// check if the object is group-readable
		if ($permissions['group']['read'] && $permits->userHasGroupOwnership($userId, $object, $this))
			return true;
		
		// check if the object is user-readable
		if ($permissions['user']['read'] && $permits->userHasOwnership($userId, $object, $this))
			return true;
		
		return false;
	}
	
	// whether the current/supplied user may update the given object
	public function mayUpdate($object, int $userId = null): bool
	{
		// load the library
		$permits = Services::permits();
		
		// if no user provided, check for a logged in user
		$userId = $userId ?? $permits->sessionUserId();

		// check for a permit
		if ($permit = $permits->hasPermit($userId, 'update' . ucfirst($this->table)))
			return true;
		
		// get the object
		$object = $this->find($id);

		// make sure permissions are setup correctly
		if (! $permits->isPermissible($object, $this))
			return false;
		$permissions = mode2array($this->rowMode);

		// check if the object is world-writeable
		if ($permissions['world']['write'])
			return true;
		
		// check if the object is group-writeable
		if ($permissions['group']['write'] && $permits->userHasGroupOwnership($userId, $object, $this))
			return true;
		
		// check if the object is user-writeable
		if ($permissions['user']['write'] && $permits->userHasOwnership($userId, $object, $this))
			return true;
		
		return false;
	}
	
	// whether the current/supplied user may delete the given object
	public function mayDelete($object, int $userId = null): bool
	{
		return $this->mayUpdate($object, $userId);
	}
	
	// whether the current/supplied user may list rows from this model's table
	public function mayList(int $userId = null): bool
	{
		// load the library
		$permits = Services::permits();
		
		// if no user provided, check for a logged in user
		$userId = $userId ?? $permits->sessionUserId();

		// check for a permit
		if ($permit = $permits->hasPermit($userId, 'list' . ucfirst($this->table)))
			return true;
		
		// make sure permissions are setup correctly
		if (! is_octal($this->tableMode))
			return false;
			
		// check if the table itself is world-readable
		if ($permissions = mode2array($this->tableMode))
			return $permissions['world']['read'];
		
		return false;
	}
}
