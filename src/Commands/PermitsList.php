<?php namespace Tatter\Permits\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class PermitsList extends BaseCommand
{
	protected $group       = 'Auth';
	protected $name        = 'permits:list';
	protected $description = 'Lists permits assigned explicitly in the database.';

	public function run(array $params)
	{
		$db = db_connect();

		// User permits
		CLI::write(' USER PERMITS ', 'white', 'black');

		// get all user permits
		$rows = $db->table('permits')->select('user_id, name, created_by, created_at')
			->where('user_id >', 0)
			->orderBy('user_id', 'asc')
			->orderBy('name', 'asc')
			->get()->getResultArray();

		if (empty($rows))
		{
			CLI::write( CLI::color('No user permits granted.', 'yellow') );
		}
		else
		{
			$thead = ['User ID', 'Permission', 'Granted By', 'Granted Date'];
			CLI::table($rows, $thead);
		}

		// Group permits
		CLI::write(' GROUP PERMITS ', 'white', 'black');

		// get all user permits
		$rows = $db->table('permits')->select('group_id, name, created_by, created_at')
			->where('group_id >', 0)
			->orderBy('group_id', 'asc')
			->orderBy('name', 'asc')
			->get()->getResultArray();

		if (empty($rows))
		{
			CLI::write( CLI::color('No group permits granted.', 'yellow') );
		}
		else
		{
			$thead = ['Group ID', 'Permission', 'Granted By', 'Granted Date'];
			CLI::table($rows, $thead);
		}
	}
}
