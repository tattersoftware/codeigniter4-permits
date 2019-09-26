<?php

use ModuleTests\Support\Models\FactoryModel;

class DatabaseTest extends ModuleTests\Support\PermitsTestCase
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
		$model->tableMode = 0777;
		
		$this->assertTrue($model->mayCreate());
	}
}
