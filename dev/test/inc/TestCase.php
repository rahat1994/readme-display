<?php

namespace Dev\Test\Inc;

class TestCase extends \PHPUnit\Framework\TestCase
{
	use Concerns, RefreshDatabase;

	protected $plugin = null;
	
	protected $factory = null;

	public function setUp() : void
	{
		parent::setUp();

        $this->bootstrap();
	}

	protected function bootstrap()
	{
		$this->plugin = $this->createApplication(__DIR__ . '/../../../');

        $this->refreshDatabaseAndResetUser();
        
        $this->factory = new Factory;

        $config = require(__DIR__.'/../config.php');

        update_option('siteUrl', $config['site_url']);

        add_filter('pre_option_home', function () use ($config) {
	        return $config['site_url'];
	    });
	}

	protected function createApplication($pluginDir)
	{
		$ns = json_decode(
			file_get_contents($pluginDir . '/composer.json'),
		)->extra->wpfluent->namespace->current;
		
		$application = $ns . '\Framework\Foundation\Application';

		return new $application(realpath($pluginDir . '/plugin.php'));
	}

	protected function refreshDatabaseAndResetUser()
	{
		$this->refreshDatabase('migrateUp');

		$this->setUser(0);
	}

	public function tearDown() : void
	{
		$this->refreshDatabase('migrateDown');
		
		$this->plugin = null;

		parent::tearDown();
	}

	public function __get($key)
	{
		if ($this->plugin->bound($key)) {
			return $this->plugin->{$key};
		}

		throw new \ErrorException(
			'Undefined property: ' . get_class(new self) . '::' . $key
		);
	}
}
