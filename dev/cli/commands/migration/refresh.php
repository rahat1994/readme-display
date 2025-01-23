<?php
/**
 * ./wpf migrate:refresh <path/name>
 * Refreshes all the tables by deleting the tables and re-running the migrations.
 */
return function($pluginDir, $args) {
	$composer = json_decode(file_get_contents($pluginDir.'/composer.json'), true);
	$ns = $composer['extra']['wpfluent']['namespace']['current'];
	$dbMigratorClass = $ns.'\\Database\\DBMigrator';
	if ($result = $dbMigratorClass::getMigrations()) {
		$db = $GLOBALS['wpdb'];
		$output = new \Symfony\Component\Console\Output\ConsoleOutput;
		foreach (array_keys($result) as $table) {
			if ($db->query("drop table if exists {$db->prefix}{$table}")) {
				$output->writeln(
					'<comment>Dropped table '.$db->prefix.$table.'.</comment>'
				);
			}
		}

		if ($result = $dbMigratorClass::run()) {
			foreach ($result as $key => $value) {
        		$output->writeln('<comment>'.$value.'.</comment>');
        	}
        	$output->writeln('<info>The migration ran successfully.</info>');
		} else {
			$output->writeln('<info>There was nothing to migrate.</info>');
		}

		if (isset($args[1]) && $args[1] === '--seed') {
    		require $pluginDir.'/dev/seeds/index.php';
    		$output->writeln('<info>The database has been seeded successfully.</info>');
		}
	}
};
