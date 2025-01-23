<?php

namespace Dev\Test\Inc;

use WP_User;
use WP_REST_Request;
use InvalidArgumentException;

trait Concerns
{
    public function get($uri, $params = [], $headers = [])
    {
        return $this->dispatch(__FUNCTION__, $uri, $params, $headers);
    }

    public function post($uri, $params = [], $headers = [])
    {
        if (isset($params['__files__'])) {

            $files = $params['__files__'];

            if (is_array($files)) {
                foreach ($files as $index => $file) {
                    $tmpFilePath = $this->makeFile($file);

                    $_FILES['file'][$index] = [
                        'name'     => basename($file),
                        'type'     => mime_content_type($file),
                        'tmp_name' => $tmpFilePath,
                        'error'    => UPLOAD_ERR_OK,
                        'size'     => filesize($file),
                    ];
                }
            } else {
                $tmpFilePath = $this->makeFile($files);

                $_FILES['file'] = [
                    'name'     => basename($files),
                    'type'     => mime_content_type($files),
                    'tmp_name' => $tmpFilePath,
                    'error'    => UPLOAD_ERR_OK,
                    'size'     => filesize($files),
                ];
            }


            $params['__files__'] = $_FILES['file'];
        }

        $response = $this->dispatch(__FUNCTION__, $uri, $params, $headers);

        $_FILES = [];

        return $response;
    }

    public function patch($uri, $params = [], $headers = [])
    {
        return $this->dispatch(__FUNCTION__, $uri, $params, $headers);
    }

    public function put($uri, $params = [], $headers = [])
    {
        return $this->dispatch(__FUNCTION__, $uri, $params, $headers);
    }

    public function delete($uri, $params = [], $headers = [])
    {
        return $this->dispatch(__FUNCTION__, $uri, $params, $headers);
    }

    public function dispatch($method, $uri, $params = [], $headers = [])
    {
        $response = rest_do_request(
            $this->createRequest($method, $uri, $params, $headers)
        );

        return new Response($response);
    }

    public function createRequest($method, $uri, $params = [], $headers = [])
    {
        do_action('rest_api_init');

        $method = strtoupper($method);

        $parsedUrl = parse_url($uri);

        $request = new WP_REST_Request(
            $method, $this->buildUrl($parsedUrl['path'])
        );

        return $this->populateSuperglobals(
            $method, $parsedUrl, $params, $uri
        )->setRequestParams(
            $request, $params
        )->setRequestHeaders($request, $headers);
    }

    protected function buildUrl($path)
    {
        return $this->getRestNamespace() . trim($path, '/');
    }

    protected function makeFile($file)
    {
        $this->assertFileExists($file, 'Test file does not exist');

        $tmpDir = sys_get_temp_dir() . '/_tmp_file_uploads';

        if (!is_dir($tmpDir)) mkdir($tmpDir, 0777, true);
        
        $tmpFilePath = $tmpDir . DIRECTORY_SEPARATOR . basename($file);
        
        copy($file, $tmpFilePath);

        return $tmpFilePath;
    }

    protected function populateSuperglobals($method, $parsedUrl, $params, $uri)
    {
        $_GET = $_POST = $_REQUEST = []; // clear super globals
        
        // Extract query parameters from URL
        // and merge them with $_GET.
        $queryParams = [];
        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $queryParams);
            $_GET = array_merge($_GET, $queryParams);
        }

        // Handle method-specific population of superglobals
        if ($method === 'GET') {
            $_GET = array_merge($_GET, $params);
        } else {
            $_POST = array_merge($_POST, $params);
        }

        // Merge $_GET and $_POST into $_REQUEST
        $_REQUEST = array_merge($_GET, $_POST);

        $c = $this->plugin->config->get('app');
        $_SERVER['REQUEST_URI'] = '/wp-json/' . $c['rest_namespace'];
        $_SERVER['REQUEST_URI'] .= '/' . $c['rest_version'] . '/' . $uri;
        
        $_SERVER['DOCUMENT_ROOT'] = realpath(__DIR__.'/../../../../../..');
        $_SERVER['SERVER_SOFTWARE'] = 'PHP Unit';
        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['HTTP_HOST'] = $siteUrl = get_option('siteUrl');
        $_SERVER['SERVER_NAME'] = $siteUrl;

        return $this;
    }

    protected function setRequestParams($request, $params)
    {
        // Set request params for rest api
        foreach ($params as $key => $value) {
            $request->set_param($key, $value);
        }

        return $this;
    }

    protected function setRequestHeaders($request, $headers = [])
    {
        $defaultHeaders = [
            'X-WP-Nonce' => wp_create_nonce('wp_rest'),
        ];

        $request->set_headers(
            array_merge($headers, $defaultHeaders)
        );

        return $request;
    }

    protected function getRestNamespace()
    {
        $ns = $this->plugin->config->get('app.rest_namespace');

        $ver = $this->plugin->config->get('app.rest_version');

        return '/' . $ns . '/' . $ver . '/';
    }

    public function login($id)
    {
        return $this->setUser($id);
    }

    public function logout()
    {
        return $this->setUser(0);
    }

    public function setUser($id)
    {
        $exception = new InvalidArgumentException(
            'The argument must be a valid user ID or WP_User object'
        );

        if (is_object($id)) {
            if ($id instanceof WP_User) {
                $id = $id->ID;
            } elseif (method_exists($id, 'getKey')) {
                $id = $id->getKey();
            }
        }

        if (is_int($id)) {
            $user = wp_set_current_user($id);

            if ($id && !$user->ID) {
                throw $exception;
            }

            return $this;
        }

        throw $exception;
    }
}
