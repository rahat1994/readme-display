<?php

class TestCommand
{
    private $args = [];
    private $loader = null;

    public function __construct($args)
    {
        $this->args = $args;
        $this->loader = $args['loader'];
        unset($args['loader']);
    }

    public function run()
    {
        $tmpDir = sys_get_temp_dir();

        $init = require $this->args['cwd'] . "/dev/cli/commands/legacy/init.php";

        $this->cleanupOnError($tmpDir, $init);

        try {
            $this->initializeTests($tmpDir, $init);
        } catch (Exception $e) {
            die($e->getMessage() . PHP_EOL);
        }
    }

    private function cleanupOnError($tmpDir, $init)
    {
        register_shutdown_function(function() use ($tmpDir, $init) {
            if (!is_null($error = error_get_last())) {
                echo PHP_EOL . 'Error: ' . print_r($error, true)  . PHP_EOL;
                echo "\nIf this error is related to the test suite, run ";
                die("./wpf init\n");
            }
        });
    }

    private function initializeTests($tmpDir, $init)
    {
        $funcPath = $tmpDir . '/wordpress-tests-lib/includes/functions.php';
        if (!file_exists($funcPath)) {
            echo "\nNeed to install the test suite, please wait..., \n\n";
            require_once $this->args['cwd'] . "/../../../wp-load.php";
            $this->deleteTestSuites($tmpDir);
            $init(['init'], $this->loader);
            die('Run ./wpf test' . PHP_EOL);
        }

        // Activate plugin during the test
        $this->activatePluginDuringTest();

        // Run PHPUnit tests
        $this->runPHPUnitTests();
    }

    private function deleteTestSuites($tmpDir)
    {
        foreach (['/wordpress', '/wordpress-tests-lib'] as $path) {
            $this->deleteRecursively($tmpDir . $path);
        }
    }

    private function deleteRecursively($dir)
    {
        if (!is_dir($dir)) {
            return;
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            $path = $dir . DIRECTORY_SEPARATOR . $item;

            if (is_dir($path)) {
                $this->deleteRecursively($path);
            } else {
                unlink($path);
            }
        }

        rmdir($dir);
    }

    private function activatePluginDuringTest()
    {
        $plugin = basename($this->args['cwd']);
        $GLOBALS['wp_tests_options'] = [
            'active_plugins' => [$plugin . '/plugin.php']
        ];
    }

    private function runPHPUnitTests()
    {
        chdir($this->args['cwd'] . '/dev');
        $args = array_merge($this->args['argsv'], ['--exclude', 'skip']);
        $command = new PHPUnit\TextUI\Command;
        $result = $command->run([
            'phpunit', ...array_splice($args, 1)], false
        );

        if ($result > 2) {
            $this->suggestTestCleanup();
        }
    }

    private function suggestTestCleanup()
    {
        $msg = 'If nothing works, try running: ' . PHP_EOL . PHP_EOL;
        $msg .= 'rm -rf "$(echo $TMPDIR)wordpress" "$(echo $TMPDIR)wordpress-tests-lib"' . PHP_EOL . PHP_EOL;
        $msg .= './wpf test or ./dev/test/setup.sh' . PHP_EOL . PHP_EOL;
        die($msg);
    }
}
