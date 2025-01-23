<?php
/**
 * ./wpf make:model      <path/name>
 * Creates a ORM model class inside the app/Models folder.
 */
(function($pluginDir, $args) {
    $composerFile = $pluginDir . '/composer.json';
    $file = $pluginDir . '/app/Models/' . $args[1] . '.php';
    $composer = json_decode(file_get_contents($composerFile), true);
    $namespace = $composer['extra']['wpfluent']['namespace']['current'];
    $pieces = explode('/', $args[1]);
    $name = array_pop($pieces);
    $sub = implode('\\', $pieces);
    $fqn = $namespace.'\App\Models\\'.ltrim($sub, '\\');

    $content = <<<TEXT
    <?php

    namespace {$fqn};

    use $namespace\App\Models\Model;

    class $name extends Model
    {
        // If the table name is not given explicitly then the plural form of
        // your model name in lower case will be used for the table name.

        // protected \$table = 'database_table_name_without_prefix';
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
        $output->writeln('<info>Model '.$mainPath.' created successfully.</info>');
    }
})(realpath(__DIR__.'/../../../..'), $args);
