<?php

namespace Tests\Support\Models;

use CodeIgniter\Model;
use Faker\Generator;
use Tatter\Permits\Traits\PermitsTrait;

class FactoryModel extends Model
{
    use PermitsTrait;

    protected $table          = 'factories';
    protected $primaryKey     = 'id';
    protected $returnType     = 'object';
    protected $useSoftDeletes = false;
    protected $useTimestamps  = true;
    protected $allowedFields  = [
        'name',
        'user_id',
    ];

    public function addFactoryToUser(int $factoryId, int $userId)
    {
        $this->db->table('factories_users')->insert([
            'factory_id' => $factoryId,
            'user_id'    => $userId,
        ]);
    }

    /**
     * Faked data for Fabricator.
     */
    public function fake(Generator &$faker): object
    {
        return (object) [
            'name' => $faker->company,
        ];
    }
}
