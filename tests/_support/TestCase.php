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
