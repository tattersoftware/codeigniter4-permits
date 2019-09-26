<?php namespace Tatter\Permits;

/***
* Name: Permits
* Author: Matthew Gatner
* Contact: mgatner@tattersoftware.com
* Created: 2019-02-12
*
* Description:  Lightweight permission handler for CodeIgniter 4
*
* Requirements:
* 	>= PHP 7.1
* 	>= CodeIgniter 4.0
*	Preconfigured, autoloaded Database
*	CodeIgniter's Session Library (loaded automatically)
* 	User model (supplied or internal) that implements PUserInterface
*	`permits` table (run migrations)
*
* Configuration:
* 	Use app/Config/Permits.php to override default behavior
* 	Run migrations to update database tables:
* 		> php spark migrate:latest -n "Tatter\Permits"
*
* @package CodeIgniter4-Permits
* @author Matthew Gatner
* @link https://github.com/tattersoftware/codeigniter4-permits
*
***/

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Config\Services;
use Tatter\Permits\Models\PermitModel;
use Tatter\Permits\Models\UserModel;
use Tatter\Permits\Exceptions\VisitsException;
use Tatter\Permits\Interfaces\PUserInterface;

/*** CLASS ***/
class Permits
{
	/**
	 * Our configuration instance.
	 *
	 * @var \Tatter\Permits\Config\Permits
	 */
	protected $config;

	/**
	 * The main database connection, needed to check permits table.
	 *
	 * @var ConnectionInterface
	 */
	protected $db;

	/**
	 * The active user session.
	 *
	 * @var \CodeIgniter\Session\Session
	 */
	protected $session;

	/**
	 * The permit model used to fetch permits.
	 *
	 * @var \Tatter\Permits\Models\PermitModel
	 */
	protected $permitModel;

	/**
	 * External model to handle users
	 *
	 * @var CodeIgniter\Model
	 */
	protected $userModel = null;

	// initiate library, check for existing session
	public function __construct(BaseConfig $config, ConnectionInterface $db = null, PUserInterface $userModel = null)
	{		
		// save configuration
		$this->config = $config;

		// initiate the Session library
		$this->session = Services::session();
		
		// load the permit model
		$this->permitModel = new PermitModel();
		
		// If no db connection passed in, use the default database group.
		$this->db = db_connect($db);
		
		// load helper for mode conversions
		helper('chmod');
		
		/*** Validations ***/
		
		// if provided user model is invalid then use the internal version
		$this->userModel = ($userModel instanceof PUserInterface)? $userModel : new UserModel();
	}
	
	// checks for a logged in user based on config
	// returns user ID, 0 for "not logged in", -1 for CLI
	public function sessionUserId(): int
	{
		if (ENVIRONMENT != 'testing' && is_cli())
			return -1;
		return $this->session->get($this->config->sessionUserId) ?? 0;
	}
	
	// try to cache a permit and pass it back
	protected function cache($key, $permit)
	{
		if ($duration = $this->config->cacheDuration)
			cache()->save($key, $permit, $duration);
		return $permit;
	}
	
	// series fo checks to ensure input is a valid object and model has permissions setup
	public function isPermissible($object, $objectModel): bool
	{
		if (! is_octal($objectModel->rowMode))
			return false;
		if (empty($object))
			return false;
		return true;
	}
	
	// checks if user is a member of the supplied group
	public function userHasGroup(int $userId, int $groupId): ?bool
	{
		if (! $this->config->useGroups)
			return null;

		foreach ($this->userModel->groups($userId) as $group):
			if ($groupId==$group->id)
				return true;
		endforeach;
		
		return false;
	}
	
	// checks if user is one of an object's owners
	public function userHasOwnership(int $userId, $object, $objectModel): bool
	{
		// make sure the model has the necessary info
		if (empty($objectModel->userKey))
			return false;
		
		// if input is an array, convert it
		if (gettype($object)=='array')
			$object = (object) $object;
			
		// check if the object itself has $userKey set
		if ($object->{$objectModel->userKey}):
			return ($userId==$object->{$objectModel->userKey});
		
		// otherwise, check for a valid pivot table
		elseif (! empty($objectModel->usersPivot)):
			$test = $objectModel->db->table($objectModel->usersPivot)
				->where($objectModel->userKey, $userId)
				->where($this->pivotKey, $object->{$objectModel->primaryKey})
				->get()->getResult();
			return ! empty($test);
		endif;
		
		return false;
	}
	
	// checks if user is a member of an object's groups
	public function userHasGroupOwnership(int $userId, $object, $objectModel): ?bool
	{
		if (! $this->config->useGroups)
			return null;
		
		// if input is an array, convert it
		if (gettype($object)=='array')
			$object = (object) $object;

		// make sure the model has the necessary info
		if (empty($objectModel->groupKey))
			return false;

		// check if the object itself has $groupKey set
		if ($groupId = $object->{$objectModel->groupKey}):
			// check if this is a group the user is a part of
			return $this->userHasGroup($userId, $groupId);
		
		// otherwise, check for a valid pivot table
		elseif (! empty($objectModel->groupsPivot)):
			$test = $objectModel->db->table($objectModel->groupsPivot)
				->where($objectModel->groupKey, $userId)
				->where($this->pivotKey, $object->{$objectModel->primaryKey})
				->get()->getResult();
			return ! empty($test);
		endif;
		
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
		$cacheKey = "permits:{$name}:{$userId}";
		$permit = cache($cacheKey);
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
			return null;

		return ! empty($this->permitModel
			->where('user_id', $userId)
			->where('name', $name)
			->first()
		);
	}
	
	// checks for global permit for one group
	public function hasGroupPermit(int $groupId, string $name): bool
	{
		if (! $this->config->useGroups)
			return null;
		if (empty($groupId))
			return null;

		return ! empty($this->permitModel
			->where('group_id', $groupId)
			->where('name', $name)
			->first()
		);		
	}
}
