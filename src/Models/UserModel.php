<?php namespace Tatter\Permits\Models;

use CodeIgniter\Model;
use Tatter\Permits\Interfaces\PermitsUserModelInterface;

/**
 * This model is supplied as a bare minimum 'read-only' toolkit for
 * the Permits library. In most cases you will want to extend this
 * as a starting point, or replace it with your own user model.
 * This model expects:
 *     - The corresponding group model, Tatter\Permits\Models\GroupModel
 *     - Tables `users`, `auth_groups`, and `auth_groups_users` (will still be prefixed)
 *     - Primary keys `id` on `users` and `auth_groups`; `user_id` and `group_id` on the pivot
 */

class UserModel extends Model implements PermitsUserModelInterface
{
	protected $table      = 'users';
	protected $primaryKey = 'id';
	protected $returnType = 'object';

	protected $useTimestamps  = false;
	protected $useSoftDeletes = false;
	protected $skipValidation = true;

	protected $allowedFields = [];

	// Permits
	protected $mode        = 00640;
	protected $pivotKey    = 'user_id';
	protected $groupsPivot = 'auth_groups_users';

	/**
	 * Returns groups for a single user.
	 *
	 * @see https://github.com/lonnieezell/myth-auth/blob/develop/src/Authorization/GroupModel.php
	 *
	 * @param mixed $userId = null
	 *
	 * @return array  Array of objects (usually Group Entities)
	 */
	public function groups($userId = null): array
	{
		return $this->builder()
			->select('auth_groups.id')
			->join($this->groupsPivot, "{$this->groupsPivot}.{$this->pivotKey} = {$this->table}.{$this->primaryKey}", 'left')
			->join('auth_groups', "{$this->groupsPivot}.group_id = auth_groups.id", 'left')
			->where("{$this->groupsPivot}.{$this->pivotKey}", $userId)
			->get()->getResultObject();
	}
}
