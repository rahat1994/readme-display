<?php
/**
 * ./wpf make:factory    <path/name>
 * Creates a factory class for generating data.
 */
(function($pluginDir, $args) {
    $file = $pluginDir . '/dev/factories/' . $args[1] . '.php';
    $pieces = explode('/', $args[1]);
    $name = array_pop($pieces);
    $sub = implode('\\', $pieces);
    $fqn = rtrim('Dev\Factories\\'.ltrim($sub, '\\'), '\\');

    $content = <<<TEXT
    <?php

    namespace $fqn;

    use Dev\Factories\Core\Factory;

    class $name extends Factory
    {
        // Required to use Factory::create method
        // protected static \$model = ModelName::class;

       /**
        * @see https://fakerphp.github.io/formatters/
        */
        public function defination()
        {
            return [
                'name' => \$this->fake->name(2),
                'email' => \$this->fake->email(),
                'phone' => \$this->fake->phoneNumber(),
            ];
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
        $output->writeln('<info>Factory '.$mainPath.' created successfully.</info>');
    }
})(realpath(__DIR__.'/../../../..'), $args);
