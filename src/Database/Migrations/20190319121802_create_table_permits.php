<?php namespace Tatter\Permits\Database\Migrations;

use CodeIgniter\Database\Migration;

class Migration_create_table_permits extends Migration
{
	public function up()
	{
		$fields = [
			'created_at'   => ['type' => 'DATETIME', 'null' => true],
			'updated_at'   => ['type' => 'DATETIME', 'null' => true],
		];
		
		$this->forge->addField('id');
		$this->forge->addField($fields);

		$this->forge->addKey('created_at');
		$this->forge->addKey('updated_at');
		
		$this->forge->createTable('permits');
	}

	public function down()
	{
		$this->forge->dropTable('permits');
	}
}
