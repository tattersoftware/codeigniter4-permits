<?php namespace Tatter\Permits\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class PermitsAdd extends BaseCommand
{
    protected $group       = 'Permits';
    protected $name        = 'permits:add';
    protected $description = "Adds a permit to the database.";
    
	protected $usage     = "permits:add [permission] [target] [id]";
	protected $arguments = [
		'permission' => "The name of the permission to grant (e.g. 'listJobs')",
		'target'     => "The type of recipient ('groups' or 'users')",
		'id'         => "The ID of the recipient (e.g. '42')",
	];

	public function run(array $params = [])
    {
		$db = db_connect();
		
		// consume or prompt for permission name
		$permission = array_shift($params);
		if (empty($permission))
			$permission = CLI::prompt("Permission to grant");
		if (empty($permission)):
			CLI::error("You must supply a permission name, e.g. 'listJobs'");
			return;
		endif;
		
		// consume or prompt for target table
		$target = array_shift($params);
		if (empty($target))
			$target = CLI::prompt('Target', ['groups', 'users']);
				
		// consume or prompt for target ID
		$id = array_shift($params);
		if (empty($id))
			$id = CLI::prompt(ucfirst(substr($target, 0, -1)) . " ID", null, 'is_natural_no_zero');

		if ($target=='groups'):
			$row['group_id'] = $id;
		elseif ($target=='users'):
			$row['user_id'] = $id;
		else:
			CLI::error("Invalid target supplied: '{$target}'");
			return;
		endif;
		$row['name'] = $permission;
				
		try
		{
			$db->table('permits')->replace($row);
		}
		catch (\Exception $e)
		{
			$this->showError($e);
		}
		
		$this->call('permits:list');
	}
}
