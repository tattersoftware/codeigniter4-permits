<?php namespace Tests\Support\Database\Seeds;

class PermitSeeder extends \CodeIgniter\Database\Seeder
{
	public function run()
	{
		/* Test Auth seeds modified from https://github.com/lonnieezell/myth-auth */
		
		// USERS
		$users = [
			[
				'email'            => 'yamira@noted.com',
				'username'         => 'light',
				'password_hash'    => password_hash('secretK33P3R', PASSWORD_DEFAULT),
			],
			[
				'email'            => 'kazuto.kirigaya@castle.org',
				'username'         => 'kirito',
				'password_hash'    => password_hash('swordsX2', PASSWORD_DEFAULT),
			],
			[
				'email'            => 'Mittelman@example.com',
				'username'         => 'Saitama',
				'password_hash'    => password_hash('1punch', PASSWORD_DEFAULT),
			],
        ];
		
		$builder = $this->db->table('users');
		
		foreach ($users as $user)
		{
			$builder->insert($user);
		}
		
		// GROUPS
		$groups = [
			[
				'name'            => 'Administrators',
				'description'     => 'Users with ultimate power',
			],
			[
				'name'            => 'Blacklisted',
				'description'     => 'Users sequestered for misconduct',
			],
			[
				'name'            => 'Puny',
				'description'     => 'Users who can do next to nothing',
			],
        ];
		
		$builder = $this->db->table('auth_groups');
		
		foreach ($groups as $group)
		{
			$builder->insert($group);
		}
		
		// GROUPS-USERS
		$rows = [
			[
				'group_id'    => 1,
				'user_id'     => 1,
			],
			[
				'group_id'    => 2,
				'user_id'     => 1,
			],
			[
				'group_id'    => 3,
				'user_id'     => 2,
			],
        ];
		
		$builder = $this->db->table('auth_groups_users');
		
		foreach ($rows as $row)
		{
			$builder->insert($row);
		}

		
		/* Industrial seeds */
		
		// FACTORIES
		$factories = [
			[
				'group_id' => 1,
				'name'     => 'Test Factory',
				'uid'      => 'test001',
				'class'    => 'Factories\Tests\NewFactory',
				'icon'     => 'fas fa-puzzle-piece',
				'summary'  => 'Longer sample text for testing',
			],
			[
				'group_id' => null,
				'name'     => 'Widget Factory',
				'uid'      => 'widget',
				'class'    => 'Factories\Tests\WidgetPlant',
				'icon'     => 'fas fa-puzzle-piece',
				'summary'  => 'Create widgets in your factory',
			],
			[
				'group_id' => 2,
				'name'     => 'Evil Factory',
				'uid'      => 'evil-maker',
				'class'    => 'Factories\Evil\MyFactory',
				'icon'     => 'fas fa-book-dead',
				'summary'  => 'Abandon all hope, ye who enter here',
			]
		];
		
		$builder = $this->db->table('factories');
		
		foreach ($factories as $factory)
		{
			$builder->insert($factory);
		}
		
		// FACTORIES-USERS
		$rows = [
			[
				'factory_id'  => 1,
				'user_id'     => 1,
			],
			[
				'factory_id'  => 2,
				'user_id'     => 1,
			],
			[
				'factory_id'  => 3,
				'user_id'     => 2,
			],
        ];
		
		$builder = $this->db->table('factories_users');
		
		foreach ($rows as $row)
		{
			$builder->insert($row);
		}
	}
}
