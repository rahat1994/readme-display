<?php

($func = function ($classes = []) use (&$func) {

	$classMap = array_map(function($key) {
		return strtolower($key);
	}, $classes = $classes ?: require __DIR__.'/../../classes.php');

	$helper = new \Symfony\Component\Console\Helper\SymfonyQuestionHelper;

    $class = $helper->ask(
    	new \Symfony\Component\Console\Input\ArgvInput,
    	new \Symfony\Component\Console\Output\ConsoleOutput,
    	new \Symfony\Component\Console\Question\ChoiceQuestion(
	        'Please select a class to view methods', $classMap
	    )
    );

	if (isset($class)) {
		$methods = get_methods($class);
		$method = $helper->ask(
	    	new \Symfony\Component\Console\Input\ArgvInput,
	    	new \Symfony\Component\Console\Output\ConsoleOutput,
	    	new \Symfony\Component\Console\Question\ChoiceQuestion(
		        'Please select a method', array_keys($methods)
		    )
	    );

	    if ($method) {

	    	$output = new \Symfony\Component\Console\Output\ConsoleOutput;

	    	$output->writeln('<info></info>');
	    	$output->writeln('<info>'. $methods[$method] . '</info>');
	    	$output->writeln('<info></info>');

			if (strtolower(trim(readline("Press enter to continue:"))) !== 'q') {
				$func();
			}
		}
	}
})(
	require __DIR__.'/../../classes.php',
);
