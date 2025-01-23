<?php

class Command
{
	public static function run(string $cmd, array $args)
	{
		$cmd = ucfirst(strtolower($cmd)) . 'Command';
		require __DIR__ . '/commands/' . $cmd . '.php';
		$cmd = new $cmd($args);
		$cmd->run();
	}
}
