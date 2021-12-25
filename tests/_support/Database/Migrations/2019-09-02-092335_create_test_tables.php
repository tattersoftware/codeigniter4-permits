<?php

namespace Tests\Support\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTestTables extends Migration
{
    public function up()
    {
        // Factories
        $fields = [
            'group_id'   => ['type' => 'int', 'null' => true],
            'name'       => ['type' => 'varchar', 'constraint' => 31],
            'uid'        => ['type' => 'varchar', 'constraint' => 31],
            'class'      => ['type' => 'varchar', 'constraint' => 63],
            'icon'       => ['type' => 'varchar', 'constraint' => 31],
            'summary'    => ['type' => 'varchar', 'constraint' => 255],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
            'deleted_at' => ['type' => 'datetime', 'null' => true],
        ];

        $this->forge->addField('id');
        $this->forge->addField($fields);

        $this->forge->addKey('name');
        $this->forge->addKey('uid');
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

        // Test Auth tables modified from https://github.com/lonnieezell/myth-auth

        // Users
        $this->forge->addField([
            'id'               => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'email'            => ['type' => 'varchar', 'constraint' => 255],
            'username'         => ['type' => 'varchar', 'constraint' => 30, 'null' => true],
            'password_hash'    => ['type' => 'varchar', 'constraint' => 255],
            'reset_hash'       => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'reset_at'         => ['type' => 'datetime', 'null' => true],
            'reset_expires'    => ['type' => 'datetime', 'null' => true],
            'activate_hash'    => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'status'           => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'status_message'   => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'active'           => ['type' => 'tinyint', 'constraint' => 1, 'null' => 0, 'default' => 0],
            'force_pass_reset' => ['type' => 'tinyint', 'constraint' => 1, 'null' => 0, 'default' => 0],
            'permissions'      => ['type' => 'text', 'null' => true],
            'created_at'       => ['type' => 'datetime', 'null' => true],
            'updated_at'       => ['type' => 'datetime', 'null' => true],
            'deleted_at'       => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('email');
        $this->forge->addUniqueKey('username');
        $this->forge->createTable('users', true);

        // Groups
        $fields = [
            'id'          => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'        => ['type' => 'varchar', 'constraint' => 255],
            'description' => ['type' => 'varchar', 'constraint' => 255],
        ];
        $this->forge->addField($fields);
        $this->forge->addKey('id', true);
        $this->forge->createTable('auth_groups', true);

        // Groups-Users
        $fields = [
            'group_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
            'user_id'  => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
        ];
        $this->forge->addField($fields);
        $this->forge->addKey(['group_id', 'user_id']);
        $this->forge->createTable('auth_groups_users', true);
    }

    public function down()
    {
        $this->forge->dropTable('factories');
        $this->forge->dropTable('factories_users');
        $this->forge->dropTable('users');
        $this->forge->dropTable('auth_groups');
        $this->forge->dropTable('auth_groups_users');
    }
}
