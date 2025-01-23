<?php

class ListCommand
{
	private $args = [];

	public function __construct($args)
	{
		$this->args = $args;
	}

	public function run()
	{
		require_once __DIR__ . '/legacy/show_commands_list.php';
	}
}
