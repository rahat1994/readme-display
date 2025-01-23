<?php
/**
 * ./wpf make:middleware <path/name>
 * Creates a middleware class inside the app/Http/Middleware folder.
 */
(function($pluginDir, $args) {
    $composerFile = $pluginDir . '/composer.json';
    $file = $pluginDir . '/app/Http/Middleware/' . $args[1] . '.php';
    $composer = json_decode(file_get_contents($composerFile), true);
    $namespace = $composer['extra']['wpfluent']['namespace']['current'];
    $pieces = explode('/', $args[1]);
    $name = array_pop($pieces);
    $sub = implode('\\', $pieces);
    $fqn = $namespace.'\App\Http\Middleware\\'.ltrim($sub, '\\');

    $content = <<<TEXT
    <?php

    namespace {$fqn};

    class $name
    {
        /**
         * Handle the request
         *
         * Note: For a before middleware the \$r will contain the request instance and
         * for the after middleware, the Response will available in the \$r variable.
         * 
         * @param  \$namespace\Framework\Request\Request|\$namespace\Framework\Response\Response \$r
         * @param  Closure \$next
         * @param  array \$params
         * @return mixed
         */
        public function handle(\$r, \Closure \$next, ...\$params)
        {
            if (isset(\$params[0]) && \$params[0] === 'something_matches') {
                return \$next(\$r);
            }

            // return false or nothing for null, the Rest API will handle the response as
            // rest_forbidden (status:403) or call \$r->abort to send a custom
            // response. This call will simply return a WP_REST_Response.
            return \$r->abort(/*int code, string message*/);
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
        $output->writeln('<info>Middleware '.$mainPath.' created successfully.</info>');
    }
})(realpath(__DIR__.'/../../../..'), $args);
