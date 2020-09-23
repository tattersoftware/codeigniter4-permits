<?php namespace Tests\Support;

class PermitsTestCase extends \CodeIgniter\Test\CIDatabaseTestCase
{
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
}
