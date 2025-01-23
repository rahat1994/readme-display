<?php
/**
 * ./wpf migrate         -----------
 * Migrates the database (Create/Delete/Modify Tables/Columns).
 */
(function($pluginDir, $args) {
	$command = reset($args);
	if ($command === 'migrate') {
		$composer = json_decode(
			file_get_contents($pluginDir.'/composer.json'), true
		);

		$ns = $composer['extra']['wpfluent']['namespace']['current'];

		$dbMigratorClass = $ns.'\\Database\\DBMigrator';
		
		if ($result = $dbMigratorClass::run()) {
			$output = new \Symfony\Component\Console\Output\ConsoleOutput;
        	$output->writeln('<info>The migration ran successfully.</info>');
        	foreach ($result as $key => $value) {
        		$output->writeln('<comment>'.$value.'.</comment>');
        	}
		}
	} elseif ($command === 'migrate:refresh') {
		return (require __DIR__.'/refresh.php')($pluginDir, $args);
	}
})(realpath(__DIR__.'/../../../..'), $args);
