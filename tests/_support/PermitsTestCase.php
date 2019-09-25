<?php namespace ModuleTests\Support;

use CodeIgniter\Session\Handlers\ArrayHandler;
use Tests\Support\Session\MockSession;;

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
     * The namespace to help us find the migration classes.
     *
     * @var string
     */
    protected $namespace = 'ModuleTests\Support';

    public function setUp(): void
    {
        parent::setUp();
        
        // Also run the module's migrations
        $this->migrations->setNamespace('Tatter\Permits');
		$this->migrations->regress(0, 'tests');
		$this->migrations->latest('tests');
		
		// Seed the database *after* test & module migrations
		$this->seeder->setPath(SUPPORTPATH . 'Database/Seeds');
		$this->seed('ModuleTests\Support\Database\Seeds\PermitSeeder');
		
        $this->mockSession();
    }
    
    /**
     * Pre-loads the mock session driver into $this->session.
     *
     * @var string
     */
    protected function mockSession()
    {
        require_once ROOTPATH . 'tests/_support/Session/MockSession.php';
        $config = config('App');
        $this->session = new MockSession(new ArrayHandler($config, '0.0.0.0'), $config);
        \Config\Services::injectMock('session', $this->session);
    }
}
