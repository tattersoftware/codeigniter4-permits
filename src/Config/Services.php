<?php namespace Tatter\Permits\Config;

use CodeIgniter\Config\BaseService;
use Tatter\Permits\Config\Permits as PermitsConfig;
use Tatter\Permits\Interfaces\PermitsUserModelInterface;
use Tatter\Permits\Permits;

class Services extends BaseService
{
	/**
	 * @param PermitsConfig|null $config
	 * @param PermitsUserModelInterface|null $userModel
	 * @param bool $getShared
	 */
    public static function permits(PermitsConfig $config = null, PermitsUserModelInterface $userModel = null, bool $getShared = true)
    {
		if ($getShared)
		{
			return static::getSharedInstance('permits', $config, $userModel);
		}

		$config = $config ?? config('Permits');

		return new Permits($config, $userModel);
	}
}
