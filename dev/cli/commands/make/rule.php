<?php
/**
 * ./wpf make:rule       <path/name>
 * Creates a custom validation rule class inside the app/Http/Rules folder.
 */
(function($pluginDir, $args) {
    $composerFile = $pluginDir . '/composer.json';
    $file = $pluginDir . '/app/Http/Rules/' . $args[1] . '.php';
    $composer = json_decode(file_get_contents($composerFile), true);
    $namespace = $composer['extra']['wpfluent']['namespace']['current'];
    $pieces = explode('/', $args[1]);
    $name = array_pop($pieces);
    $sub = implode('\\', $pieces);
    $fqn = $namespace.'\App\Http\Rules\\'.ltrim($sub, '\\');

    $content = <<<TEXT
    <?php

    namespace {$fqn};

    class $name
    {
        public function __invoke(\$attr, \$value, \$rules, \$data, ...\$params)
        {
            // \$params = ['param1', 'param2'] (Passed from method call)
            // i.e: Rule::isValidPassword('param1', 'param2')

            if (!true) {
                return "The {\$attr} field must contain special characters.";
            }
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
        $output->writeln('<info>Rule '.$mainPath.' created successfully.</info>');
    }
})(realpath(__DIR__.'/../../../..'), $args);
