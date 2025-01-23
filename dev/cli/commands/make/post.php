<?php
/**
 * ./wpf make:post       <path/name>
 * Creates a custom post class to register CPT inside the app/CPT folder.
 */
(function($pluginDir, $args) {
    $composerFile = $pluginDir . '/composer.json';
    $file = $pluginDir . '/app/CPT/' . $args[1] . '.php';
    $composer = json_decode(file_get_contents($composerFile), true);
    $namespace = $composer['extra']['wpfluent']['namespace']['current'];
    $pieces = explode('/', $args[1]);
    $name = array_pop($pieces);
    $sub = implode('\\', $pieces);
    $fqn = $namespace.'\App\CPT\\'.ltrim($sub, '\\');

    $postTypeName = strtolower($name);
    $appConfig = require $pluginDir . '/config/app.php';
    $textDomain = $appConfig['text_domain'];

    $content = <<<TEXT
    <?php

    namespace {$fqn};

    class $name
    {
        public function registerPostType()
        {
            \$slug = '$postTypeName';

            \$labels = [
                'name'          => __('$name', '$textDomain'),
                'singular_name' => __('$name', '$textDomain'),
            ];

            register_post_type(\$slug, [
                'labels'                => \$labels,
                'public'                => true,
                'show_in_rest'          => true,
                'show_ui'               => false,
                'show_in_nav_menus'     => false,
                'description'           => 'Custom post type used in Foo plugin.',
            ]);

            \$this->registerTaxonomies(\$slug);
        }

        protected function registerTaxonomies(\$slug)
        {
            // ...
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
        $output->writeln('<info>Post '.$mainPath.' created successfully.</info>');
    }
})(realpath(__DIR__.'/../../../..'), $args);