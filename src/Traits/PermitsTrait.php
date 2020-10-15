<?php namespace Tatter\Permits\Traits;

use Tatter\Permits\Exceptions\PermitsException;

trait PermitsTrait
{
	// Whether the current/supplied user may insert rows into this model's table
	public function mayCreate(int $userId = null): bool
	{
		// Check for admin permit
		if ($this->mayAdmin($userId))
		{
			return true;
		}

		// Load the library and check for a user
		$permits = service('permits');
		$userId  = $userId ?? $permits->sessionUserId();

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
		$permits = service('permits');
		$userId  = $userId ?? $permits->sessionUserId();

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
		$permits = service('permits');
		$userId  = $userId ?? $permits->sessionUserId();

		// Check for a permit
		if ($permit = $permits->hasPermit($userId, 'update' . ucfirst($this->table)))
		{
			return true;
		}

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
		$permits = service('permits');
		$userId  = $userId ?? $permits->sessionUserId();

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
		$permits = service('permits');
		$userId  = $userId ?? $permits->sessionUserId();

		// Check for the permit
		if ($permit = $permits->hasPermit($userId, 'admin' . ucfirst($this->table)))
		{
			return true;
		}

		// Deny all other requests
		return false;
	}

	//--------------------------------------------------------------------

	/**
	 * Changes the access mode.
	 *
	 * @param int $mode Integer representation of octal mode. Default 04664
	 *
	 * @return $this
	 */
	public function setMode(int $mode): self
	{
		helper('chmod');
		if (! is_octal($mode))
		{
			throw new PermitsException($this->table, $mode);
		}
		$this->mode = $mode;

		return $this;
	}

	/**
	 * Returns the access mode.
	 *
	 * @return int Integer representation of octal mode. Default 04664
	 */
	public function getMode(): int
	{
		return $this->mode;
	}
}
