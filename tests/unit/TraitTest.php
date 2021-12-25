<?php

use CodeIgniter\Test\CIUnitTestCase;
use Tests\Support\Models\FactoryModel;

/**
 * @internal
 */
final class TraitTest extends CIUnitTestCase
{
    /**
     * @var FactoryModel
     */
    protected $model;

    protected function setUp(): void
    {
        parent::setUp();

        $this->model = new FactoryModel();
    }

    public function testGetMode()
    {
        $result = $this->model->getMode();

        $this->assertSame(04660, $result);
    }

    public function testSetMode()
    {
        $mode = 06600;

        $this->model->setMode($mode);

        $result = $this->model->getMode();

        $this->assertSame($mode, $result);
    }
}
