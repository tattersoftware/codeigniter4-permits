<?php namespace Tatter\Permits\Models;

/**
 * 
 * This model is supplied as a bare minimum 'read-only' toolkit for
 * the Permits library. In most cases you will want to extend this
 * as a starting point, or replace it with your own user model.
 * This model expects:
 * 	The corresponding group model, Tatter\Permits\Models\GroupModel
 * 	Tables `users`, `groups`, and `groups_users` (will still be prefixed)
 * 	Primary keys `id` on `users` and `groups`; `user_id` and `group_id` on the pivot
 *
 */

use Tatter\Permits\Interfaces\PUserInterface;

class UserModel extends PModel implements PUserInterface
{
	protected $table      = 'users';
	protected $primaryKey = 'id';

	protected $returnType = 'object';
	protected $useSoftDeletes = false;

	protected $allowedFields = [ ];

	protected $useTimestamps = false;

	protected $validationRules    = [];
	protected $validationMessages = [];
	protected $skipValidation     = true;
	
	// permits
	protected $mode = 0640;
	protected $pivotKey = 'user_id';
	protected $groupsPivot = 'groups_users';
	
	// https://github.com/lonnieezell/myth-auth/blob/develop/src/Authorization/GroupModel.php
	public function groups($userId = null): array
	{
		return $this->builder()
			->select('groups.id')
			->join($this->groupsPivot, "{$this->groupsPivot}.{$this->pivotKey} = {$this->table}.{$this->primaryKey}", 'left')
			->join('groups', "{$this->groupsPivot}.group_id = groups.id", 'left')
			->where("{$this->groupsPivot}.{$this->pivotKey}", $userId)
			->get()->getResultObject();
	}
}
