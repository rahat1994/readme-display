<?php

class TranslateCommand
{
	private $args = [];

	public function __construct($args)
	{
		$this->args = $args;
	}

	public function run()
	{
		(require $this->args['cwd'] . '/dev/cli/commands/legacy/translate.php')(
			$this->args['argsv']
		);
	}
}