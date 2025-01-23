<?php

class RoutesCommand
{
    private $args = [];

    public function __construct($args)
    {
        $this->args = $args;
    }

    public function run()
    {
        $slug = $this->args['config']['slug'];

        $response = wp_remote_get(
            home_url('/wp-json/' . $slug . '/v2/' . $slug . '/__endpoints')
        );

        $code = wp_remote_retrieve_response_code($response);

        if ($code == 404) {
            throw new Exception('Make sure that plugin is active.'.PHP_EOL);
        }

        $routes = json_decode(wp_remote_retrieve_body($response), true);

        $this->outputRouteTable($routes);
    }

    private function outputRouteTable($routes)
    {
        $rows = [];
        foreach ($routes as $controller => $actions) {
            $controller = str_replace('.', '\\', $controller);
            foreach ($actions as $action => $details) {
                $uri = $details['uri'];
                $methods = implode(', ', $details['methods']);
                $actionFormatted = $controller . '@' . ltrim($action, '_');
                $rows[] = ['methods' => $methods, 'uri' => $uri, 'action' => $actionFormatted];
            }
        }

        $this->printTable($rows, ['methods', 'uri', 'action']);
    }

    private function printTable($rows, $columns)
    {
        $colWidths = [];
        foreach ($columns as $col) {
            $colWidths[$col] = max(
                array_map('strlen', array_column($rows, $col))
            ) + 4;
        }

        echo sprintf("%-{$colWidths['methods']}s %-{$colWidths['uri']}s %-{$colWidths['action']}s", 'Method', 'URI', 'Action') . PHP_EOL;
        
        echo str_repeat('_', array_sum($colWidths) + 4) . PHP_EOL;

        foreach ($rows as $row) {
            echo sprintf(
                "%-{$colWidths['methods']}s %-{$colWidths['uri']}s %-{$colWidths['action']}s",
                $row['methods'], $row['uri'], $row['action']
            ) . PHP_EOL;
        }
    }
}
