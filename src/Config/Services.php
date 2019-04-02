<?php namespace Tatter\Permits\Config;

use CodeIgniter\Config\BaseService;
use CodeIgniter\Database\ConnectionInterface;
use Tatter\Permits\Interfaces\PUserInterface;
use Tatter\Permits\Interfaces\PGroupInterface;

class Services extends BaseService
{
    public static function permits(BaseConfig $config = null, ConnectionInterface $db = null, PUserInterface $userModel = null, bool $getShared = true)
    {
		if ($getShared):
			return static::getSharedInstance('permits', $db, $userModel, $config);
		endif;

		// prioritizes user config in app/Config if found
		if (empty($config)):
			if (class_exists('\Config\Permits')):
				$config = new \Config\Permits();
			else:
				$config = new \Tatter\Permits\Config\Permits();
			endif;
		endif;

		return new \Tatter\Permits\Permits($config, $db, $userModel);
	}
}
