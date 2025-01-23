<?php

class SeedCommand
{
	private $args = [];

	public function __construct($args)
	{
		$this->args = $args;
	}

	public function run()
	{
		require $this->args['cwd'] . '/dev/seeds/index.php';

        (new \Symfony\Component\Console\Output\ConsoleOutput)->writeln(
            '<info>The database has been seeded successfully.</info>'
        );
	}
}