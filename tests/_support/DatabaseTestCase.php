<?php namespace ModuleTests\Support;

class DatabaseTestCase extends \CodeIgniter\Test\CIDatabaseTestCase
{
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
    }
}
