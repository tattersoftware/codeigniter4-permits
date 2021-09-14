<?php

namespace Tatter\Permits\Models;

use CodeIgniter\Model;

class PermitModel extends Model
{
	protected $table      = 'permits';
	protected $primaryKey = 'id';

	protected $returnType     = 'object';
	protected $useSoftDeletes = false;

	protected $allowedFields = [
		'name',
		'user_id',
		'group_id',
		'created_by',
	];

	protected $useTimestamps = true;

	protected $validationRules    = [];
	protected $validationMessages = [];
	protected $skipValidation     = false;
}
