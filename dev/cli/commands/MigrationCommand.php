<?php

class MigrationCommand
{
	private $args = [];

	public function __construct($args)
	{
		$this->args = $args;
	}

	public function run()
	{
		$args = $this->args['argsv'];
        require $this->args['cwd'] . '/dev/cli/commands/migration/index.php';
	}
}