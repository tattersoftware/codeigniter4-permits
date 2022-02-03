<?php

use Tatter\Imposter\Entities\User;
use Tatter\Imposter\Factories\ImposterFactory;
use Tatter\Permits\Config\Permits;
use Tests\Support\Models\FactoryModel;
use Tests\Support\TestCase;

/**
 * @internal
 */
final class InferredTest extends TestCase
{
    protected ?User $user = null;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test user
        if ($this->user === null) {
            $this->user     = ImposterFactory::fake();
            $this->user->id = ImposterFactory::add($this->user);
            service('auth')->login($this->user);
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        ImposterFactory::reset();
        $this->resetServices();
    }

    /**
     * @dataProvider accessProvider
     */
    public function testInferred(int $access)
    {
        $model = new FactoryModel();

        foreach (['admin', 'create', 'list'] as $verb) {
            $model->setPermits([$verb => $access]);
            $method = 'may' . ucfirst($verb);

            $this->assertSame($access >= Permits::USERS, $model->{$method}());
        }
    }

    public function accessProvider()
    {
        return [
            [Permits::ANYBODY],
            [Permits::USERS],
            [Permits::OWNERS],
            [Permits::NOBODY],
        ];
    }
}
