<?php

namespace ReadmeDisplay\App\Hooks\Handlers;

use ReadmeDisplay\App\App;
use ReadmeDisplay\App\Utils\Enqueuer\Enqueue;

class AdminMenuHandler
{
    /**
     * $app Application instance
     * @var WPFluent\Foundation\Application
     */
    protected $app;

    /**
     * $app Config instance
     * @var WPFluent\Foundation\Config
     */
    protected $config;

    /**
     * $position Menu Position
     * @var int|float
     */
    protected $position = 6;


    /**
     * Construct the instance
     */
    public function __construct()
    {
        $this->app = App::make();
        $this->config = $this->app->config;
    }

    /**
     * Add Custom Menu
     * 
     * @return null
     */
    public function add()
    {
        add_menu_page(
            __('Readme Display', 'readmedisplay'),
            __('Readme Display', 'readmedisplay'),
            'manage_options',
            $this->config->get('app.slug'),
            [$this, 'render'],
            $this->getMenuIcon(),
            $this->position
        );
    }

    /**
     * Render the menu page
     * 
     * @return null
     */
    public function render()
    {
        $this->enqueueAssets(
            $slug = $this->config->get('app.slug')
        );

        $baseUrl = $this->app->applyFilters(
            'fluent_connector_base_url',
            admin_url('admin.php?page=' . $slug . '#/')
        );

        $this->app->view->render('admin.menu', [
            'name' => $this->config->get('app.name'),
            'slug' => $slug,
            'baseUrl' => $baseUrl,
            'menuItems' => $this->getMenuItems($baseUrl),
            'logo' => Enqueue::getStaticFilePath('images/logo.svg'),
        ]);
    }

    /**
     * Get and map menu items for main nav.
     * 
     * @param  string $baseUrl
     * @return array
     */
    protected function getMenuItems($baseUrl)
    {
        $menuItems = [
            [
                'key' => 'plugins',
                'label' => __('Plugin', 'readmedisplay'),
                'permalink' => $baseUrl
            ]
        ];

        return $this->app->applyCustomFilters(
            'admin_menu_items',
            $menuItems
        );
    }

    /**
     * Enqueue all the scripts and styles
     * 
     * @param  string $slug
     * @return null
     */
    public function enqueueAssets($slug)
    {
        Enqueue::style($slug . '_admin_app', 'scss/admin.scss');

        $this->app->doAction($slug . '_loading_app');

        Enqueue::script(
            $slug . '_admin_app',
            'admin/app.js',
            ['wp-api-fetch'],
            '1.0',
            true
        );

        // wp_set_script_translations(
        //     $slug . '_admin_app',
        //     $this->config->get('app.text_domain'),
        //     $this->app['path'] . 'language'
        // );

        $this->localizeScript($slug);
    }

    /**
     * Push/Localize the JavaScript variables
     * 
     * to the browser using wp_localize_script.
     * 
     * @param  string $slug
     * @return null
     */
    protected function localizeScript($slug)
    {
        $authUser = get_user_by('ID', get_current_user_id());

        wp_localize_script($slug . '_admin_app', 'fluentFrameworkAdmin', [
            'slug' => $slug,
            'user_locale' => get_locale(),
            'brand_logo' => $this->getMenuIcon(),
            'nonce' => wp_create_nonce($slug),
            'asset_url' => $this->app['url.assets'],
            'rest' => $r = $this->getRestInfo(),
            'endpoints' => $this->getRestEndpoinds($r),
            'me' => [
                'id' => $authUser->ID ?? null,
                'email' => $authUser->user_email ?? null,
                'full_name' => $authUser->display_name ?? null
            ],
        ]);
    }

    /**
     * Gether rest info/settings for http client.
     * 
     * @return array
     */
    protected function getRestInfo()
    {
        $ns = $this->app->config->get('app.rest_namespace');
        $ver = $this->app->config->get('app.rest_version');

        return [
            'base_url' => $this->getBaseRestUrl(),
            'url' => $this->getFullRestUrl($ns, $ver),
            'nonce' => wp_create_nonce('wp_rest'),
            'namespace' => $ns,
            'version' => $ver
        ];
    }

    /**
     * Get base rest url by examining the permalink.
     * 
     * @see https://wordpress.stackexchange.com/questions/273144/can-i-use-rest-api-on-plain-permalink-format
     * 
     * @return string
     */
    protected function getBaseRestUrl()
    {
        if (get_option('permalink_structure')) {
            return esc_url_raw(rest_url());
        }

        return esc_url_raw(
            rtrim(get_site_url(), '/') . "/?rest_route=/"
        );
    }

    /**
     * Get the full rest url by examining the permalink
     * (full means, including the namespace/version).
     * 
     * @param $ns Rest Namespace
     * @param $ver Rest Version
     * @see https://wordpress.stackexchange.com/questions/273144/can-i-use-rest-api-on-plain-permalink-format
     * 
     * @return string
     */
    protected function getFullRestUrl($ns, $ver)
    {
        if (get_option('permalink_structure')) {
            return esc_url_raw(rest_url($ns . '/' . $ver));
        }

        return esc_url_raw(
            rtrim(get_site_url(), '/') . "/?rest_route=/{$ns}/{$ver}"
        );
    }

    /**
     * Retrieve rest endpoints for client.
     * 
     * @param  array $r Rest Info
     * @return array
     */
    protected function getRestEndpoinds($r)
    {
        $url = $r['url'] . '/' . $r['namespace'] . '/__endpoints';

        $slug = $this->config->get('app.slug');

        $result = wp_remote_get($url, [
            'sslverify' => false,
            'cookies' => $_COOKIE,
            'user-agent' => "wpfluent.{$slug}.__endpoints",
            'headers' => [
                'X-Wp-Nonce' => $r['nonce']
            ]
        ]);

        if (wp_remote_retrieve_response_code($result) === 200) {
            return json_decode(wp_remote_retrieve_body($result), true);
        }

        if (is_wp_error($result)) {
            $message = $result->get_error_message();
        } else {
            $message = 'WordPress rest request failed.';
        }

        wp_die(
            "<div class='notice notice-error update-nag'>
                {$this->config->get('app.name')} - {$message}
            </div>"
        );
    }

    /**
     * Get the default icon for custom menu
     * added by the add_menu in the WP menubar.
     * 
     * @return string
     */
    protected function getMenuIcon()
    {
        return 'dashicons-wordpress-alt';
    }

    /**
     * Makes the class invokable.
     * 
     * @return null
     */
    public function __invoke()
    {
        $this->add();
    }
}

