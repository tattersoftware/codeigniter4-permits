<?php

namespace Tests\Support\Models;

use Tatter\Permits\Model;

class FactoryModel extends Model
{
    protected $table          = 'factories';
    protected $primaryKey     = 'id';
    protected $returnType     = 'object';
    protected $useSoftDeletes = false;
    protected $allowedFields  = [
        'group_id',
        'name',
        'uid',
        'class',
        'icon',
        'summary',
    ];
    protected $useTimestamps      = true;
    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    // Permits
    public $mode       = 04660;
    public $groupKey   = 'group_id';
    public $pivotKey   = 'factory_id';
    public $usersPivot = 'factories_users';
}
