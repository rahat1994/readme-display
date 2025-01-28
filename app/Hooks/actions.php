<?php

use ReadmeDisplay\App\Hooks\Handlers\ShortcodeHandler;

/**
 * All registered action's handlers should be in app\Hooks\Handlers,
 * addAction is similar to add_action and addCustomAction is just a
 * wrapper over add_action which will add a prefix to the hook name
 * using the plugin slug to make it unique in all wordpress plugins,
 * ex: $app->addCustomAction('foo', ['FooHandler', 'handleFoo']) is
 * equivalent to add_action('slug-foo', ['FooHandler', 'handleFoo']).
 */

/**
 * @var $app ReadmeDisplay\Framework\Foundation\Application
 */

$app->addAction('admin_menu', 'AdminMenuHandler');
$app->addShortcode('readme-display', [ShortcodeHandler::class, 'add']);
$app->addAction('plugins_loaded', function () {

	$currentDBVersion = get_option('readme_display_db_version');
	if (!$currentDBVersion || version_compare($currentDBVersion, FLUENT_COMMUNITY_DB_VERSION, '<')) {
		update_option('fluent_community_db_version', RD_DB_VERSION, false);
		\ReadmeDisplay\Database\DBMigrator::run();
	}
});
/**
 * Enable this line if you want to use custom post types
 */

// $app->addAction('init', 'CPTHandler@registerPostTypes');

/**
 * This is being used to update the slug when idle.
 */
$app->addFilter('heartbeat_send', function ($response, $data) use ($app) {
	$key = $app->config->get('app.slug');
	$response[$key] = wp_create_nonce('wp_rest');
	return $response;
}, 10, 2);
