<?php namespace Tatter\Permits;

use CodeIgniter\Config\Services;

class Model extends \CodeIgniter\Model
{
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
	
	// Whether the current/supplied user may insert rows into this model's table
	public function mayCreate(int $userId = null): bool
	{
		// Check for admin permit
		if ($this->mayAdmin($userId))
		{
			return true;
		}
		
		// Load the library and check for a user
		$permits = Services::permits();
		$userId = $userId ?? $permits->sessionUserId();

		// Check for a permit
		if ($permit = $permits->hasPermit($userId, 'create' . ucfirst($this->table)))
		{
			return true;
		}
		
		// Make sure the mode is setup correctly
		if (! is_octal($this->mode))
		{
			return false;
		}
			
		// Check for domain writeable (create)
		if ($permissions = mode2array($this->mode))
		{
			return $permissions['domain']['write'];
		}

		return false;
	}
	
	// Whether the current/supplied user may read the given object
	public function mayRead($object, int $userId = null): bool
	{
		// Check for admin permit
		if ($this->mayAdmin($userId))
		{
			return true;
		}
		
		// Load the library and check for a user
		$permits = Services::permits();
		$userId = $userId ?? $permits->sessionUserId();

		// Check for an explicit permit
		if ($permit = $permits->hasPermit($userId, 'read' . ucfirst($this->table)))
		{
			return true;
		}
		
		// Make sure permissions are setup correctly
		if (! $permits->isPermissible($object, $this))
		{
			return false;
		}
		$permissions = mode2array($this->mode);
		
		// Check if the object is world-readable
		if ($permissions['world']['read'])
		{
			return true;
		}
		
		// Check if the object is group-readable
		if ($permissions['group']['read'] && $permits->userHasGroupOwnership($userId, $object, $this))
		{
			return true;
		}
		
		// Check if the object is user-readable
		if ($permissions['user']['read'] && $permits->userHasOwnership($userId, $object, $this))
		{
			return true;
		}
		
		return false;
	}
	
	// Whether the current/supplied user may update the given object
	public function mayUpdate($object, int $userId = null): bool
	{
		// Check for admin permit
		if ($this->mayAdmin($userId))
		{
			return true;
		}
		
		// Load the library and check for a user
		$permits = Services::permits();
		$userId = $userId ?? $permits->sessionUserId();

		// Check for a permit
		if ($permit = $permits->hasPermit($userId, 'update' . ucfirst($this->table)))
		{
			return true;
		}
		
		// Get the object
		$object = $this->find($id);

		// Make sure permissions are setup correctly
		if (! $permits->isPermissible($object, $this))
		{
			return false;
		}
		$permissions = mode2array($this->mode);

		// Check if the object is world-writeable
		if ($permissions['world']['write'])
		{
			return true;
		}
		
		// Check if the object is group-writeable
		if ($permissions['group']['write'] && $permits->userHasGroupOwnership($userId, $object, $this))
		{
			return true;
		}
		
		// Check if the object is user-writeable
		if ($permissions['user']['write'] && $permits->userHasOwnership($userId, $object, $this))
		{
			return true;
		}
		
		return false;
	}
	
	// Whether the current/supplied user may delete the given object
	public function mayDelete($object, int $userId = null): bool
	{
		// Check for admin permit
		if ($this->mayAdmin($userId))
		{
			return true;
		}
		
		return $this->mayUpdate($object, $userId);
	}
	
	// Whether the current/supplied user may list rows from this model's table
	public function mayList(int $userId = null): bool
	{
		// Check for admin permit
		if ($this->mayAdmin($userId))
		{
			return true;
		}
		
		// Load the library and check for a user
		$permits = Services::permits();
		$userId = $userId ?? $permits->sessionUserId();

		// Check for a permit
		if ($permit = $permits->hasPermit($userId, 'list' . ucfirst($this->table)))
		{
			return true;
		}
		
		// Make sure permissions are setup correctly
		if (! is_octal($this->mode))
		{
			return false;
		}
			
		// Check if the domain is readable
		if ($permissions = mode2array($this->mode))
		{
			return $permissions['domain']['read'];
		}
		
		return false;
	}
	
	// Whether the current/supplied user may perform any of the other actions
	public function mayAdmin(int $userId = null): bool
	{
		// Load the library and check for a user
		$permits = Services::permits();
		$userId = $userId ?? $permits->sessionUserId();

		// Check for the permit
		if ($permit = $permits->hasPermit($userId, 'admin' . ucfirst($this->table)))
		{
			return true;
		}
		
		// Deny all other requests
		return false;
	}
}
