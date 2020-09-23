<?php namespace Tatter\Permits\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Tatter\Permits\Models\PermitModel;

class PermitsAdd extends BaseCommand
{
	protected $group       = 'Auth';
	protected $name        = 'permits:add';
	protected $description = 'Adds a permit to the database.';

	protected $usage     = 'permits:add [permission] [target] [id]';
	protected $arguments = [
		'permission' => "The name of the permission to grant (e.g. 'listJobs')",
		'target'     => "The type of recipient ('groups' or 'users')",
		'id'         => "The ID of the recipient (e.g. '42')",
	];

	public function run(array $params = [])
	{
		$permits = new PermitModel();

		// Consume or prompt for the permission name
		$permission = array_shift($params);
		if (empty($permission))
		{
			$permission = CLI::prompt('Permission to grant', null, 'required');
		}

		// Consume or prompt for the target table
		$target = array_shift($params);
		if (empty($target))
		{
			$target = CLI::prompt('Target', ['groups', 'users']);
		}

		// Consume or prompt for the target ID
		$id = array_shift($params);
		if (empty($id))
		{
			$id = CLI::prompt(ucfirst(substr($target, 0, -1)) . ' ID', null, 'is_natural_no_zero');
		}

		if ($target === 'groups')
		{
			$row['group_id'] = $id;
		}
		else
		{
			$row['user_id'] = $id;
		}
		$row['name'] = $permission;

		try
		{
			$permits->save($row);
		}
		catch (\Exception $e)
		{
			$this->showError($e);
		}

		$this->call('permits:list');
	}
}
