<?php

class MakeCommand
{
	private $args = [];

	public function __construct($args)
	{
		$this->args = $args;	
	}

	public function run()
	{
		$args = $this->args['argsv'];
		$arg = reset($args);
        $command = ltrim(substr($arg, 4), ':');
        $files = glob($this->args['cwd'] . '/dev/cli/commands/make/*.php');
        $file = $this->args['cwd'] . "/dev/cli/commands/make/{$command}.php";

        if (in_array($file, $files)) {
            require $file;
        } else {
            throw new Exception($command);
        }
	}
}
