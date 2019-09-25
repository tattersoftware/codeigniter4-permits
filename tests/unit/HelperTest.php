<?php

class HelperTest extends \CodeIgniter\Test\CIUnitTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        helper('chmod');
    }

	public function testIsOctalTrue()
	{
		$this->assertTrue(is_octal(0001));
		$this->assertTrue(is_octal(0604));
		$this->assertTrue(is_octal(0777));
	}

	public function testIsOctalFalse()
	{
		$this->assertFalse(is_octal('0001'));
		$this->assertFalse(is_octal(777));
	}

	public function testMode2Array()
	{
		$array = mode2array(0755);
		$this->assertEmpty($array['world']['write']);
		$this->assertNotEmpty($array['user']['execute']);
	}
}
