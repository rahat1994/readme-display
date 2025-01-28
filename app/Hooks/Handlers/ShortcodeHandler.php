<?php

namespace ReadmeDisplay\App\Hooks\Handlers;

use ReadmeDisplay\App\App;
use ReadmeDisplay\App\Parser\PluginReadmeParser;
use ReadmeDisplay\App\Utils\Enqueuer\Enqueue;
use ReadmeDisplay\App\Parser\Parsedown;
class ShortcodeHandler
{

    public $parseDown;
    public $pluginReadmeParser;

    public $readmeContent = '';
    public function __construct(ParseDown $parseDown, PluginReadmeParser $pluginReadmeParser)
    {
        $this->parseDown = $parseDown;
        $this->pluginReadmeParser = $pluginReadmeParser;
    }

    /**
     * Handle the Shortcode
     * 
     * @return string | null
     */
    public function add($atts = [], $content = null, $tag = '')
    {
        $shortcodeAtts = $this->getShortcodeAttributes($atts, $tag);

        $slug = $shortcodeAtts['plugin'];

        if (!$slug) {
            return;
        }

        $readmeData = $this->getReadmeData($slug);

        if (!$readmeData) {
            return;
        }

        return $readmeData;
    }

    public function getReadmeData($slug = '')
    {

        $url = 'https://plugins.svn.wordpress.org/' . $slug . '/trunk/readme.txt';

        $response = wp_remote_get($url);

        if (is_wp_error($response)) {
            return false;
        }

        $body = wp_remote_retrieve_body($response);

        if (!$body) {
            return false;

        }

        $metas = $this->extractPluginMeta($body);

        $cleaned_content = $this->remove_meta_from_beginning($body);

        $sections_to_remove = ['Screenshots', 'Changelog', 'Frequently Asked Questions'];

        // Remove the specified sections
        $cleaned_content = $this->removeSections($cleaned_content, $sections_to_remove);
        // Convert sections with === at the start and end to <h1> tags
        $cleaned_content = preg_replace('/^===\s*(.*?)\s*===$/m', '<h1>$1</h1>', $cleaned_content);
        $cleaned_content = preg_replace('/^==\s*(.*?)\s*==$/m', '<h2>$1</h2>', $cleaned_content);
        $cleaned_content = preg_replace('/^=\s*(.*?)\s*=$/m', '<p>$1</p>', $cleaned_content);

        $this->parseDown->setBreaksEnabled(true);
        $cleaned_content = $this->parseDown->text($cleaned_content);

        $cleaned_content = $this->wrapIframesWithDiv($cleaned_content);

        // $changeLog = $this->pluginReadmeParser->parseChangelog($body);
        // $faq = $this->pluginReadmeParser->parseFaq($body);

        // return $changeLog;

        return $cleaned_content;
    }


    public function removeSections($content, $sections)
    {
        foreach ($sections as $section) {
            // Create a regular expression to match the section and its content
            $pattern = "/==\s*{$section}\s*==.*?(?=(==\s*|$))/s";
            $content = preg_replace($pattern, '', $content);
        }
        return $content;
    }

    public function wrapIframesWithDiv($content)
    {
        // Define the regular expression to find iframe elements
        $pattern = '/<iframe\b[^>]*>(.*?)<\/iframe>/is';

        // Define the replacement pattern to wrap iframes in a div with class "iframe-wrapper"
        $replacement = '<div class="iframe-wrapper" style="text-align:center">$0</div>';

        // Perform the replacement
        $wrapped_content = preg_replace($pattern, $replacement, $content);

        return $wrapped_content;
    }


    public function remove_meta_from_beginning($content)
    {
        // Define the regular expression to match the metadata lines at the beginning
        $pattern = '/^(.*?)(?=\n==)/s';

        // Remove the matched metadata lines from the content
        $cleaned_content = preg_replace($pattern, '', $content, 1);

        return $cleaned_content;
    }

    /**
     * Retrieves and normalizes shortcode attributes.
     *
     * This method normalizes the attribute keys to lowercase and overrides the default attributes with user-provided attributes.
     *
     * @param array|string $atts The user-defined shortcode attributes.
     * @param string $tag The name of the shortcode.
     * @return array The normalized and merged shortcode attributes.
     */
    public function getShortcodeAttributes($atts, $tag)
    {
        // normalize attribute keys, lowercase
        $atts = array_change_key_case((array) $atts, CASE_LOWER);

        // override default attributes with user attributes
        return shortcode_atts(
            array(
                'plugin' => null,
            ),
            $atts,
            $tag
        );
    }

    public function extractPluginMeta($content)
    {
        // extract the plugin meta
        // like
        // Contributors: techjewel,adreastrian,heera,pyrobd,hrdelwar,dhrupo,wpmanageninja
        // Tags: contact form, wp forms, forms, form builder, custom form
        // Requires at least: 4.5
        // Tested up to: 6.7
        // Requires PHP: 7.4
        // Stable tag: 5.2.10
        // License: GPLv2 or later
        // License URI: https://www.gnu.org/licenses/gpl-2.0.html

        $pattern = '/^(Contributors|Tags|Requires at least|Tested up to|Requires PHP|Stable tag|License|License URI):\s*(.+)$/m';

        // Perform a regular expression match
        if (preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
            $meta = [];
            foreach ($matches as $match) {
                $meta[$match[1]] = $match[2];
            }
            return $meta;
        }

        return [];
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
}

