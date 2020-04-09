<?php namespace Tests\Support;

use CodeIgniter\Session\Handlers\ArrayHandler;
use CodeIgniter\Test\Mock\MockSession;;

class PermitsTestCase extends \CodeIgniter\Test\CIDatabaseTestCase
{
	/**
	 * @var SessionHandler
	 */
	protected $session;


	/**
	 * Should the database be refreshed before each test?
	 *
	 * @var boolean
	 */
	protected $refresh = true;

	/**
	 * The seed file(s) used for all tests within this test case.
	 * Should be fully-namespaced or relative to $basePath
	 *
	 * @var string|array
	 */
	protected $seed = 'Tests\Support\Database\Seeds\PermitSeeder';

	/**
	 * The path to the seeds directory.
	 * Allows overriding the default application directories.
	 *
	 * @var string
	 */
	protected $basePath = SUPPORTPATH . 'Database/';

	/**
	 * The namespace(s) to help us find the migration classes.
	 * Empty is equivalent to running `spark migrate -all`.
	 * Note that running "all" runs migrations in date order,
	 * but specifying namespaces runs them in namespace order (then date)
	 *
	 * @var string|array|null
	 */
	protected $namespace = [
		'Tests\Support',
		'Tatter\Permits',
	];

	public function setUp(): void
	{
		parent::setUp();
		
		$this->mockSession();
	}
	
	/**
	 * Pre-loads the mock session driver into $this->session.
	 *
	 * @var string
	 */
	protected function mockSession()
	{
		$config = config('App');
		$this->session = new MockSession(new ArrayHandler($config, '0.0.0.0'), $config);
		\Config\Services::injectMock('session', $this->session);
	}
}
