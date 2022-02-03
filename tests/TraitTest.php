<?php

use CodeIgniter\Model;
use Tatter\Imposter\Factories\ImposterFactory;
use Tatter\Permits\Config\Permits;
use Tatter\Permits\Traits\PermitsTrait;
use Tests\Support\Entities\MockUser;
use Tests\Support\Models\FactoryModel;
use Tests\Support\TestCase;

/**
 * @internal
 */
final class TraitTest extends TestCase
{
    protected FactoryModel $model;

    protected function setUp(): void
    {
        parent::setUp();

        $this->model = new FactoryModel();
    }

    public function testGetPermitsUsesDefault()
    {
        $model = new class () extends Model {
            use PermitsTrait;

            protected $table = 'foo';
        };

        $config = new Permits();
        $result = $model->getPermits();

        $this->assertSame($config->default, $result);
    }

    public function testGetPermitsMergesConfigValues()
    {
        $config            = config('Permits');
        $config->factories = [
            'admin'      => Permits::ANYBODY,
            'pivotTable' => 'bananas',
        ];

        $result = $this->model->getPermits();

        $this->assertSame(Permits::ANYBODY, $result['admin']);
        $this->assertSame('bananas', $result['pivotTable']);
        $this->assertSame($config->default['list'], $result['list']);
    }

    public function testAdminAlwaysAllowed()
    {
        $this->model->setPermits([
            'admin'  => Permits::NOBODY,
            'create' => Permits::NOBODY,
            'list'   => Permits::NOBODY,
        ]);
        $user              = ImposterFactory::fake();
        $user->permissions = ['factories.admin'];

        $method = $this->getPrivateMethodInvoker($this->model, 'userHasPermission');

        $this->assertTrue($method($user, 'admin'));
        $this->assertTrue($method($user, 'create'));
        $this->assertTrue($method($user, 'list'));
    }

    public function testRequiresHasPermission()
    {
        $user   = new MockUser();
        $method = $this->getPrivateMethodInvoker($this->model, 'userHasPermission');

        $this->assertFalse($method($user, 'list'));
    }

    public function testUnknownUserThrows()
    {
        $this->expectException('UnexpectedValueException');
        $this->expectExceptionMessage('User provider was unable to locate a User with ID: 42');

        $method = $this->getPrivateMethodInvoker($this->model, 'permissible');
        $method('admin', 42);
    }

    public function testExplicitOverrides()
    {
        $user              = ImposterFactory::fake();
        $user->permissions = ['factories.create'];
        $user->id          = ImposterFactory::add($user);
        service('auth')->login($user);

        $model = new FactoryModel();
        $model->setPermits(['create' => Permits::NOBODY]);

        $this->assertTrue($model->mayCreate());
    }
}
