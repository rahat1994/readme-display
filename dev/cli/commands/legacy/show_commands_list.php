<?php

(function() {
	$commands = array_map(function($c) {
		$doc = parse_docblock(file_get_contents($c));
		$name = substr($c = basename($c), 0, strpos($c, '.'));
		return ['command' => reset($doc), 'description' => end($doc)];
	}, glob(__DIR__.'/../*/*.php'));

	$commands[] = [
		'command' => './wpf build             -----------',
		'description' => 'Builds the plugin zip file.'
	];
	
	$commands[] = [
		'command' => './wpf fix             -----------',
		'description' => 'To make each composer package unique, it adds the current namespace as prefix where applicable.'
	];

	$commands[] = [
		'command' => './wpf seed            -----------',
		'description' => 'Runs the db database seeders to seed the database tables.'
	];

	$commands[] = [
		'command' => './wpf test            [--options]',
		'description' => 'Runs the tests. It\'s possible to use all the options of phpunit.'
	];

	$commands[] = [
		'command' => './wpf doc             -----------',
		'description' => 'Opens an interactive shell to search/view method signature and docblock.'
	];

	array_unshift($commands, [
		'command' => './wpf routes          -----------',
		'description' => 'Displays registered rest routes.'
	]);

	array_unshift($commands, [
		'command' => './wpf about           -----------',
		'description' => 'Displays information about the application.'
	]);

	array_unshift($commands, [
		'command' => './wpf                 -----------',
		'description' => 'Shows the table of all available commands.'
	]);

	(new \Symfony\Component\Console\Helper\Table(
		new \Symfony\Component\Console\Output\ConsoleOutput
	))->setHeaders(['Command', 'Description'])->setRows($commands)->render();
})();
