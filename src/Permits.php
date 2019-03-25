<?php namespace Tatter\Permits;

/***
* Name: Permits
* Author: Matthew Gatner
* Contact: mgatner@tattersoftware.com
* Created: 2019-02-12
*
* Description:  Lightweight permission handler for CodeIgniter 4
*
* Requirements:
* 	>= PHP 7.1
* 	>= CodeIgniter 4.0
*	Preconfigured, autoloaded Database
*	Permits table (run migrations)
*
* Configuration:
* 	Use app/Config/Permits.php to override default behavior
* 	Run migrations to update database tables:
* 		> php spark migrate:latest -n "Tatter\Permits"
*
* @package CodeIgniter4-Permits
* @author Matthew Gatner
* @link https://github.com/tattersoftware/codeigniter4-permits
*
***/

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Config\Services;
use Tatter\Permits\Entities\Permit;
use Tatter\Permits\Models\PermitModel;
use Tatter\Permits\Exceptions\VisitsException;

/*** CLASS ***/
class Permits
{
	/**
	 * Our configuration instance.
	 *
	 * @var \Tatter\Permits\Config\Permits
	 */
	protected $config;

	/**
	 * The main database connection, needed to check permits table.
	 *
	 * @var ConnectionInterface
	 */
	protected $db;

	/**
	 * The active user session.
	 *
	 * @var \CodeIgniter\Session\Session
	 */
	protected $session;

	// initiate library, check for existing session
	public function __construct(BaseConfig $config, $db = null)
	{		
		// save configuration
		$this->config = $config;

		// initiate the Session library
		$this->session = Services::session();
		
		// If no db connection passed in, use the default database group.
		$this->db = db_connect($db);
		
		// validations
		
	}
}
