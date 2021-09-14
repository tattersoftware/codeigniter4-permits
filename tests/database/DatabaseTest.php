<?php

use Tests\Support\Models\FactoryModel;

/**
 * @internal
 */
final class DatabaseTest extends \Tests\Support\PermitsTestCase
{
	public function testMayList()
	{
		session()->logged_in = 2;

		$model = new FactoryModel();

		$this->assertTrue($model->mayList());
	}

	public function testMayCreate()
	{
		session()->logged_in = 2;

		$model       = new FactoryModel();
		$model->mode = 02640;

		$this->assertTrue($model->mayCreate());
	}
}
