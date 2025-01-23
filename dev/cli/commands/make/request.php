<?php
/**
 * ./wpf make:request    <path/name>
 * Creates a request class inside the app/Http/Requests folder.
 */
(function($pluginDir, $args) {
    $composerFile = $pluginDir . '/composer.json';
    $file = $pluginDir . '/app/Http/Requests/' . $args[1] . '.php';
    $composer = json_decode(file_get_contents($composerFile), true);
    $namespace = $composer['extra']['wpfluent']['namespace']['current'];
    $pieces = explode('/', $args[1]);
    $name = array_pop($pieces);
    $sub = implode('\\', $pieces);
    $fqn = $namespace.'\App\Http\Requests\\'.ltrim($sub, '\\');
    $fqn = rtrim($fqn, '\\');

    $content = <<<TEXT
    <?php

    namespace {$fqn};

    use $namespace\Framework\Validator\Rule;
    use $namespace\Framework\Foundation\RequestGuard;

    class $name extends RequestGuard
    {
        /**
         * Register your custom rules
         */
        public function __construct()
        {
            // Rule::add(CustomRule::class);
        }

        /**
         * Authorize the request
         * 
         * @return bool
         */
        public function authorize()
        {
            return true;
        }

        /**
         * @return Array
         */
        public function rules()
        {
            return [];
        }

        /**
         * @return Array
         */
        public function messages()
        {
            return [];
        }

        /**
         * @return Array
         */
        public function beforeValidation()
        {
            \$data = \$this->all();
            
            // Modify the \$data

            return \$data;
        }

        /**
         * @return Array
         */
        public function afterValidation(\$validator)
        {
            \$data = \$this->all();
            
            // Modify the \$data

            return \$data;
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
        $output->writeln('<info>Request '.$mainPath.' created successfully.</info>');
    }
})(realpath(__DIR__.'/../../../..'), $args);
