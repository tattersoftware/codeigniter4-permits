<?php

use Tests\Support\Models\FactoryModel;

class DatabaseTest extends \Tests\Support\PermitsTestCase
{
	public function testMayList()
	{
		$this->session->logged_in = 2;
		
		$model = new FactoryModel();
		
		$this->assertTrue($model->mayList());
	}
	
	public function testMayCreate()
	{
		$this->session->logged_in = 2;
		
		$model = new FactoryModel();
		$model->mode = 02640;
		
		$this->assertTrue($model->mayCreate());
	}
}
