<?php namespace ModuleTests\Support\Models;

use CodeIgniter\Model;
use Tatter\Permits\Models\PModel;

class FactoryModel extends PModel
{
	protected $table      = 'factories';
	protected $primaryKey = 'id';

	protected $returnType = 'object';
	protected $useSoftDeletes = false;

	protected $allowedFields = ['group_id', 'name', 'uid', 'class', 'icon', 'summary'];

	protected $useTimestamps = true;

	protected $validationRules    = [];
	protected $validationMessages = [];
	protected $skipValidation     = false;
	
	// Permits
	public $tableMode  = 0664;
	public $rowMode    = 0660;
	public $groupKey   = 'group_id';
	public $pivotKey   = 'factory_id';
	public $usersPivot = 'factories_users';
}
