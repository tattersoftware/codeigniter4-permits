<?php

namespace Tests\Support;

use CodeIgniter\Test\CIUnitTestCase;
use Tatter\Imposter\Factories\ImposterFactory;
use Tatter\Users\UserProvider;

/**
 * @internal
 */
abstract class TestCase extends CIUnitTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        helper(['auth']);
        UserProvider::addFactory(ImposterFactory::class, ImposterFactory::class);
    }
}

/*
    // Permits
    public $mode       = 04660;
    public $groupKey   = 'group_id';
    public $pivotKey   = 'factory_id';
    public $usersPivot = 'factories_users';
*/
