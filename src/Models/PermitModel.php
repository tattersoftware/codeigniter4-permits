<?php namespace Tatter\Permits\Models;

use CodeIgniter\Model;

class PermitModel extends Model
{
	protected $table      = 'permits';
	protected $primaryKey = 'id';

	protected $returnType = 'Tatter\Permits\Entities\Permit';
	protected $useSoftDeletes = false;

	protected $allowedFields = [];

	protected $useTimestamps = true;

	protected $validationRules    = [];
	protected $validationMessages = [];
	protected $skipValidation     = false;

}
