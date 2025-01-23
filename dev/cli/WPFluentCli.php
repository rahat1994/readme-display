<?php

require __DIR__ . '/Command.php';

class WPFluentCLI
{
    private $args;
    private $cwd;
    private $loader;
    private $devGlobals;
    private $functions;
    private $config;

    public function __construct($argv, $cwd, $loader, $devGlobals, $functions)
    {
        $this->cwd = $cwd;
        $this->loader = $loader;
        $this->functions = $functions;
        $this->devGlobals = $devGlobals;
        $this->args = array_slice($argv, 1);

        // If dev/vendor/autoload.php missing
        if (!file_exists($loader)) {
            $this->init();
        }

        // Load required files
        $this->requireFiles([$loader, $devGlobals, $functions]);

        // Load config.app
        $this->config = require $cwd . '/config/app.php';
    }

    private function requireFiles($files)
    {
        foreach ($files as $file) {
            if (file_exists($file)) {
                require $file;
            } else {
                die($file . ' file does not exist' . PHP_EOL);
            }
        }
    }

    private function maybeLoadWordPress()
    {
        $args ??= $this->args[0];

        if (!defined('ABSPATH') && $args && $args !== 'test') {
            require_once $this->cwd."/../../../wp-load.php";
        }
    }

    public function run()
    {
        $this->maybeLoadWordPress();

        if (empty($this->args)) {
            return $this->showCommandsList();
        }

        $command = strtolower($this->args[0]);

        if (method_exists($this, $command)) {
            return $this->{$command}();
        } else {
            $command = substr($this->args[0], 0, 4);
            if (method_exists($this, $command)) {
                return $this->{$command}();
            }
        }

        $this->unknownCommand($this->args[0]);
    }

    private function getArgs()
    {
        return [
            'cwd' => $this->cwd,
            'argsv' => $this->args,
            'config' => $this->config
        ];
    }

    private function about()
    {
        Command::run(__FUNCTION__, $this->getArgs());
    }

    private function showCommandsList()
    {
        Command::run('list', $this->getArgs());
    }

    private function routes()
    {
        try {
            Command::run(__FUNCTION__, $this->getArgs());
        } catch (Exception $e) {
            $message = '<error>'.$e->getMessage().'</error>';
            $this->writeLine($message);
        }
    }

    private function build()
    {
        Command::run(__FUNCTION__, $this->getArgs());
    }

    private function test()
    {
        $this->maybeReplaceNamespace();
        
        $args = array_merge(
            $this->getArgs(), [
                'loader' => $this->loader
            ]
        );

        Command::run(__FUNCTION__, $args);
    }

    private function translate()
    {
        Command::run(__FUNCTION__, $this->getArgs());
    }

    private function make()
    {
        try {
            Command::run('make', $this->getArgs());
        } catch (Exception $e) {
            $this->unknownCommand(null, $e->getMessage());
        }
    }

    private function migr()
    {
        Command::run('migration', $this->getArgs());
    }

    private function seed()
    {
        Command::run('seed', $this->getArgs());
    }

    private function doc()
    {
        require $this->cwd . '/dev/cli/commands/legacy/show_doc_viewer.php';
    }

    private function init()
    {
        (require $this->cwd . '/dev/cli/commands/legacy/init.php')(
            $this->args, $this->cwd . '/dev/vendor/autoload.php'
        );
    }

    private function fix()
    {
        require $this->cwd . '/dev/cli/commands/legacy/update_static.php';
    }

    private function logmon()
    {
        exec("pkill -f 'logmon'");
        exec('php ' . $this->cwd . '/dev/cli/logmon.php > /dev/null 2>&1 &');
        echo "Log monitor started...\n";
    }

    private function logoff()
    {
        exec("pkill -f 'logmon'");
        echo "Log monitor stopped.\n";
    }

    private function maybeReplaceNamespace()
    {
        // Update __NAMESPACE to real namespace in
        // the tests folder if needed (first time)
        $composer = json_decode(
            file_get_contents(__DIR__.'/../../composer.json'), true
        );
        $ns = $composer['extra']['wpfluent']['namespace']['current'];

        $files = glob(__DIR__ . '/../factories/*.php');
        $files[] = __DIR__ . '/../test/tests/TestSample.php';

        foreach ($files as $file) {
            if (!file_exists($file)) continue;
            $content = file_get_contents($file);
            $content = str_replace('__NAMESPACE', $ns, $content);
            file_put_contents($file, $content);
        }
    }

    private function unknownCommand($arg, $command = null)
    {
        $message = '<error>Unknown command ' . (
            isset($command) ? "make:{$command}" : $arg
        ) . '.</error>';

        $this->writeline($message);
    }

    private function writeLine($message)
    {
        $output = (new \Symfony\Component\Console\Output\ConsoleOutput);
        $output->writeln('');
        $output->writeln($message);
    }
}
