<?php namespace Tatter\Permits\Config;

use CodeIgniter\Config\BaseService;
use CodeIgniter\Database\ConnectionInterface;
use Tatter\Permits\Interfaces\PermitsUserModelInterface;

class Services extends BaseService
{
    public static function permits(BaseConfig $config = null, ConnectionInterface $db = null, PermitsUserModelInterface $userModel = null, bool $getShared = true)
    {
		if ($getShared):
			return static::getSharedInstance('permits', $db, $userModel, $config);
		endif;

		// If no config was injected then load one
		// Prioritizes app/Config if found
		if (empty($config))
			$config = config('Permits');

		return new \Tatter\Permits\Permits($config, $db, $userModel);
	}
}
