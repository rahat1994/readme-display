<?php
/**
 * ./wpf make:policy     <path/name>
 * Creates a policy class inside the app/Http/Policies folder.
 */
(function($pluginDir, $args) {
    $composerFile = $pluginDir . '/composer.json';
    $file = $pluginDir . '/app/Http/Policies/' . $args[1] . '.php';
    $composer = json_decode(file_get_contents($composerFile), true);
    $namespace = $composer['extra']['wpfluent']['namespace']['current'];
    $pieces = explode('/', $args[1]);
    $name = array_pop($pieces);
    $sub = implode('\\', $pieces);
    $fqn = $namespace.'\App\Http\Policies\\'.ltrim($sub, '\\');

    $content = <<<TEXT
    <?php

    namespace {$fqn};

    use $namespace\Framework\Request\Request;
    use $namespace\Framework\Foundation\Policy;

    class $name extends Policy
    {
        /**
         * Check user permission for any method
         * @param  $namespace\Framework\Request\Request \$request
         * @return Boolean
         */
        public function verifyRequest(Request \$request)
        {
            return current_user_can('manage_options');
        }

        /**
         * Check user permission for create method
         * @param  $namespace\Framework\Request\Request \$request
         * @return Boolean
         */
        public function create(Request \$request)
        {
            return current_user_can('manage_options');
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
        $output->writeln('<info>Policy '.$mainPath.' created successfully.</info>');
    }
})(realpath(__DIR__.'/../../../..'), $args);
