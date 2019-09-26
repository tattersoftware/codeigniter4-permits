<?php namespace Tatter\Permits\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePermitsTables extends Migration
{
	public function up()
	{
		$fields = [
			'name'         => ['type' => 'VARCHAR', 'constraint' => 63],
			'user_id'      => ['type' => 'INT', 'null' => true],
			'group_id'     => ['type' => 'INT', 'null' => true],
			'created_by'   => ['type' => 'INT', 'null' => true],
			'created_at'   => ['type' => 'DATETIME', 'null' => true],
			'updated_at'   => ['type' => 'DATETIME', 'null' => true],
		];
		
		$this->forge->addField('id');
		$this->forge->addField($fields);

		$this->forge->addKey('name');
		$this->forge->addKey(['user_id', 'name']);
		$this->forge->addKey(['group_id', 'name']);
		$this->forge->addKey('created_at');
		
		$this->forge->createTable('permits');
	}

	public function down()
	{
		$this->forge->dropTable('permits');
	}
}
