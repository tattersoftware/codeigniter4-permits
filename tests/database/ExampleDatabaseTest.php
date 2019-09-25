<?php

class ExampleDatabaseTest extends ModuleTests\Support\DatabaseTestCase
{
	public function setUp(): void
	{
		parent::setUp();
	}

	public function testDatabaseSimple()
	{
		$model = new \ModuleTests\Support\Models\FactoryModel();

		$objects = $model->findAll();

		$this->assertCount(3, $objects);
	}
}
