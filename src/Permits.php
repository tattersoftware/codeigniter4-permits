<?php namespace Tatter\Permits;

use Config\Services;
use Tatter\Permits\Config\Permits as PermitsConfig;
use Tatter\Permits\Interfaces\PermitsUserModelInterface;
use Tatter\Permits\Models\PermitModel;
use Tatter\Permits\Models\UserModel;

class Permits
{
	/**
	 * Our configuration instance.
	 *
	 * @var PermitsConfig
	 */
	protected $config;

	/**
	 * The permit model used to fetch permits.
	 *
	 * @var PermitModel
	 */
	protected $permitModel;

	/**
	 * External model to handle users
	 *
	 * @var PermitsUserModelInterface
	 */
	protected $userModel = null;

	/**
	 * Initializes the library.
	 *
	 * @param PermitsConfig                  $config
	 * @param PermitsUserModelInterface|null $userModel
	 */
	public function __construct(PermitsConfig $config, PermitsUserModelInterface $userModel = null)
	{
		$this->config = $config;

		// Load the models
		$this->userModel   = $userModel ?? model(UserModel::class);
		$this->permitModel = model(PermitModel::class);

		// Load the helper for mode conversions
		helper('chmod');
	}

	/**
	 * Checks for a logged in user based on the configured key.
	 *
	 * @return integer  The user ID, 0 for "not logged in", -1 for CLI
	 */
	public function sessionUserId(): int
	{
		if (ENVIRONMENT !== 'testing' && is_cli())
		{
			return -1;
		}

		return session($this->config->sessionUserId) ?? 0;
	}

	// try to cache a permit and pass it back
	protected function cache($key, $permit)
	{
		if ($duration = $this->config->cacheDuration)
		{
			cache()->save($key, $permit, $duration);
		}
		return $permit;
	}

	// series fo checks to ensure input is a valid object and model has permissions setup
	public function isPermissible($object, $objectModel): bool
	{
		if (! is_octal($objectModel->mode))
		{
			return false;
		}
		if (empty($object))
		{
			return false;
		}
		return true;
	}

	// checks if user is a member of the supplied group
	public function userHasGroup(int $userId, int $groupId): ?bool
	{
		if (! $this->config->useGroups)
		{
			return null;
		}

		foreach ($this->userModel->groups($userId) as $group)
		{
			if ($groupId === $group->id)
			{
				return true;
			}
		}

		return false;
	}

	// checks if user is one of an object's owners
	public function userHasOwnership(int $userId, $object, $objectModel): bool
	{
		// make sure the model has the necessary info
		if (empty($objectModel->userKey))
		{
			return false;
		}

		// if input is an array, convert it
		if (gettype($object) === 'array')
		{
			$object = (object) $object;
		}

		// check if the object itself has $userKey set
		if ($object->{$objectModel->userKey})
		{
			return ($userId === $object->{$objectModel->userKey});

			// otherwise, check for a valid pivot table
		}
		elseif (! empty($objectModel->usersPivot))
		{
			// @phpstan-ignore-next-line
			return (bool) $objectModel->db->table($objectModel->usersPivot)->where($objectModel->userKey, $userId)->where($objectModel->pivotKey, $object->{$objectModel->primaryKey})
				->get()->getResult();
		}

		return false;
	}

	// checks if user is a member of an object's groups
	public function userHasGroupOwnership(int $userId, $object, $objectModel): ?bool
	{
		if (! $this->config->useGroups)
		{
			return null;
		}

		// if input is an array, convert it
		if (gettype($object) === 'array')
		{
			$object = (object) $object;
		}

		// make sure the model has the necessary info
		if (empty($objectModel->groupKey))
		{
			return false;
		}

		// check if the object itself has $groupKey set
		if ($groupId = $object->{$objectModel->groupKey})
		{
			// check if this is a group the user is a part of
			return $this->userHasGroup($userId, $groupId);

			// otherwise, check for a valid pivot table
		}
		elseif (! empty($objectModel->groupsPivot))
		{
			// @phpstan-ignore-next-line
			return (bool) $objectModel->db->table($objectModel->groupsPivot)->where($objectModel->groupKey, $userId)->where($objectModel->pivotKey, $object->{$objectModel->primaryKey}) // @phpstan-ignore-line
				->get()->getResult();
		}

		return false;
	}

	// check if user has direct permit or inherited group permit
	public function hasPermit(?int $userId, string $name): bool
	{
		if (empty($userId))
		{
			return false;
		}

		// check for cached version
		$cacheKey = "permits-{$name}-{$userId}";
		$permit   = cache($cacheKey);
		if ($permit !== null)
		{
			return ! empty($this->cache($cacheKey, $permit));
		}

		// check database for user permit
		if ($permit = $this->hasUserPermit($userId, $name))
		{
			return ! empty($this->cache($cacheKey, $permit));
		}

		if (! $this->config->useGroups)
		{
			return false;
		}

		// check database for each of user's groups
		foreach ($this->userModel->groups($userId) as $group)
		{
			if ($permit = $this->hasGroupPermit($group->id, $name))
			{
				return $this->cache($cacheKey, $permit);
			}
		}

		return false;
	}

	// checks for global permit for one user, ignoring groups
	public function hasUserPermit(int $userId, string $name): ?bool
	{
		if (empty($userId))
		{
			return null;
		}

		// @phpstan-ignore-next-line
		return (bool) $this->permitModel
			->where('user_id', $userId)
			->where('name', $name)
			->first();
	}

	// checks for global permit for one group
	public function hasGroupPermit(int $groupId, string $name): ?bool
	{
		if (! $this->config->useGroups)
		{
			return null;
		}
		if (empty($groupId))
		{
			return null;
		}

		// @phpstan-ignore-next-line
		return ! empty($this->permitModel
			->where('group_id', $groupId)
			->where('name', $name)
			->first()
		);
	}
}
