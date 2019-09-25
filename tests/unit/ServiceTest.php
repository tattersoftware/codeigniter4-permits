<?php

use ModuleTests\Support\Models\FactoryModel;

class ServiceTest extends \CodeIgniter\Test\CIUnitTestCase
{
	// Instance of our service
	protected $permits;
	
	public function setUp(): void
	{
		parent::setUp();
		
		$this->permits = service('Permits');
	}

	public function testIsPermissibleTrue()
	{
		$object = new \stdClass();
		$object->name = 'foobar';
		
		$model = new FactoryModel();
		
		$this->assertTrue($this->permits->isPermissible($object, $model));
	}

	public function testIsPermissibleFalseWithoutObject()
	{
		$model = new FactoryModel();
		
		$this->assertFalse($this->permits->isPermissible(null, $model));
	}

	public function testIsPermissibleFalseWithStringRowMode()
	{
		$object = new \stdClass();
		$object->name = 'foobar';
		
		$model = new FactoryModel();
		$model->rowMode = '755';
		
		$this->assertFalse($this->permits->isPermissible($object, $model));
	}
}
