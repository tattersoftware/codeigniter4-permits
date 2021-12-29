<?php

namespace Tests\Support\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTestTables extends Migration
{
    public function up()
    {
        // Factories
        $fields = [
            'name'       => ['type' => 'varchar', 'constraint' => 255],
            'user_id'    => ['type' => 'int', 'null' => true],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
            'deleted_at' => ['type' => 'datetime', 'null' => true],
        ];

        $this->forge->addField('id');
        $this->forge->addField($fields);

        $this->forge->addKey('name');
        $this->forge->addKey('user_id');
        $this->forge->addKey(['deleted_at', 'id']);
        $this->forge->addKey('created_at');

        $this->forge->createTable('factories');

        // Factories-Users
        $fields = [
            'factory_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
            'user_id'    => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
        ];

        $this->forge->addField($fields);
        $this->forge->addKey(['factory_id', 'user_id']);
        $this->forge->createTable('factories_users', true);
    }

    public function down()
    {
        $this->forge->dropTable('factories_users');
        $this->forge->dropTable('factories');
    }
}
