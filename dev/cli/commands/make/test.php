<?php
/**
 * ./wpf make:test       <path/name>
 * Creates a test class for writing unit tests.
 */
(function($pluginDir, $args) {
    $file = $pluginDir . '/dev/test/tests/' . $args[1] . '.php';
    $pieces = explode('/', $args[1]);
    $name = array_pop($pieces);
    $sub = implode('\\', $pieces);
    $fqn = 'Dev\Test\Tests\\'.trim($sub, '\\');
    $fqn = trim($fqn, '\\');

    $content = <<<TEXT
    <?php

    namespace $fqn;

    use Dev\Test\Inc\TestCase;

    class $name extends TestCase
    {
        public function test()
        {
            \$this->assertTrue(true);
        }
    }

    TEXT;

    $dirPath = substr($file, 0, strrpos($file, '/')) . '/';

    if (!is_dir($dirPath)) {
        @mkdir($dirPath, 0777, true);
    }

    if (file_put_contents($file, $content)) {
        $mainPath = substr(
            $file, strpos($file, ltrim(substr($pluginDir, strrpos($pluginDir, '/')), '/'))
        );
        $output = new \Symfony\Component\Console\Output\ConsoleOutput;
        $output->writeln('<info>Test '.$mainPath.' created successfully.</info>');
    }
})(realpath(__DIR__.'/../../../..'), $args);
