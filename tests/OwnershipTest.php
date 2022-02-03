<?php

use CodeIgniter\Test\DatabaseTestTrait;
use Tatter\Imposter\Entities\User;
use Tatter\Imposter\Factories\ImposterFactory;
use Tatter\Permits\Config\Permits;
use Tests\Support\Models\FactoryModel;
use Tests\Support\TestCase;

/**
 * @internal
 */
final class OwnershipTest extends TestCase
{
    use DatabaseTestTrait;

    protected $refresh    = false;
    protected $namespace  = 'Tests\Support';
    protected ?User $user = null;
    protected FactoryModel $model;
    protected object $byKey;
    protected object $byPivot;
    protected object $unowned;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test user and objects
        if ($this->user === null) {
            $this->user     = ImposterFactory::fake();
            $this->user->id = ImposterFactory::add($this->user);
            service('auth')->login($this->user);

            $this->model = new FactoryModel();

            $this->unowned = fake(FactoryModel::class);
            $this->byKey   = fake(FactoryModel::class, ['user_id' => $this->user->id]);
            $this->byPivot = fake(FactoryModel::class);
            $this->model->addFactoryToUser($this->byPivot->id, $this->user->id); // @phpstan-ignore-line
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        ImposterFactory::reset();
        $this->resetServices();
    }

    public function testOwnershipRequiresUserKey()
    {
        $this->model->setPermits([
            'read'       => Permits::OWNERS,
            'userKey'    => null,
            'pivotKey'   => 'factory_id',
            'pivotTable' => 'factories_users',
        ]);

        $this->assertFalse($this->model->mayRead($this->byKey));
    }

    public function testOwnershipRequiresPivotKey()
    {
        $this->model->setPermits([
            'read'       => Permits::OWNERS,
            'userKey'    => 'user_id',
            'pivotKey'   => null,
            'pivotTable' => 'factories_users',
        ]);

        $this->assertFalse($this->model->mayRead($this->byPivot));
    }

    public function testOwnershipRequiresPivotTable()
    {
        $this->model->setPermits([
            'read'       => Permits::OWNERS,
            'userKey'    => 'user_id',
            'pivotKey'   => 'factory_id',
            'pivotTable' => null,
        ]);

        $this->assertFalse($this->model->mayRead($this->byPivot));
    }

    /**
     * @dataProvider accessProvider
     */
    public function testOwnership(int $access)
    {
        foreach (['read', 'update', 'delete'] as $verb) {
            $this->model->setPermits([
                $verb        => $access,
                'userKey'    => 'user_id',
                'pivotKey'   => 'factory_id',
                'pivotTable' => 'factories_users',
            ]);
            $method = 'may' . ucfirst($verb);

            $this->assertSame($access >= Permits::OWNERS, $this->model->{$method}($this->byKey));
            $this->assertSame($access >= Permits::OWNERS, $this->model->{$method}($this->byPivot));
            $this->assertSame($access >= Permits::USERS, $this->model->{$method}($this->unowned));
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
