<?php
$wpRootDir = realpath(__DIR__ . '/../../../../../../../');

return function($args, $loader) use ($wpRootDir) {
	if (!file_exists($loader)) {	
		echo 'Please wait...'.PHP_EOL;
		chdir(__DIR__.'/../../../../dev');
		exec('composer update', $output);
		foreach ($output as $line) {
		    echo $line . PHP_EOL;
		}
	}

	if (($args && reset($args) === 'init') || !file_exists($loader)) {
		
		if (file_exists($f = __DIR__ . '/../../../../dev/test/setup.sh')) {
		    !defined('ABSPATH') && require_once $wpRootDir."/wp-load.php";
		    @chmod($f, 0700);
			$_ = $GLOBALS['wpdb'];
			if (isset($args[1]) && $args[1] === '--config') {
				if (file_exists($filePath = ABSPATH.'wp-tests-config.php')) {
					$file = fopen($filePath, 'r');
					while (!feof($file)) {
					    $line = fgets($file);
					    if (strpos($line, 'DB_NAME') !== false) {
					        preg_match(
					        	"/define\s*\(\s*'DB_NAME'\s*,\s*'([^']+)'\s*\)/", $line, $matches
					        );

					        if (isset($matches[1])) {
					            $dbn = $matches[1];
					        }
					    }
					}
					fclose($file);
				}
			} else {
				$dbn = str_replace('-', '_', basename(ABSPATH).'_testdb');
			}

			exec($f.' '.$dbn.' '.$_->dbuser.' '.$_->dbpassword.' '.$_->dbhost, $out);
			foreach ($out as $o) echo $o.PHP_EOL;

			$tmpDir = sys_get_temp_dir();
			$testConf = $tmpDir . '/wordpress-tests-lib/wp-tests-config.php';
			$newTestConf = $wpRootDir . '/wp-tests-config.php';
			if (!file_exists($newTestConf)) {
				@chmod($wpRootDir, 0700);
				@touch($newTestConf);
			}

			@unlink($newTestConf);
			@symlink($testConf, $newTestConf);
			
			if (!file_exists($testLog = $tmpDir.'/wordpress/wp-content/debug.log')) {
				@touch($testLog);
			}

			$devDir = __DIR__.'/../../..';
			@chmod($devDir, 0700);
			@unlink($devDir.'/test.log');
			@symlink($testLog, $devDir.'/test.log');

			$newLine = "defined('WP_DEBUG_LOG') || define('WP_DEBUG_LOG', true);";
			$currentContent = file_get_contents($newTestConf);
			$newContent = $currentContent . "\n" . $newLine;
			file_put_contents($newTestConf, $newContent);
		}

		return true;
	}
};
