<?php

namespace Tatter\Permits\Config;

use CodeIgniter\Config\BaseConfig;

class Permits extends BaseConfig
{
    /**
     * Whether to include groups when considering access rights.
     */
    public $useGroups = true;
}
