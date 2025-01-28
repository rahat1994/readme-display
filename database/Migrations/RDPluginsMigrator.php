<?php
// phpcs:disable
namespace ReadmeDisplay\Database\Migrations;

class RDPluginsMigrator
{
    /**
     * Migrate the table.
     *
     * @return void
     */
    public static function migrate()
    {
        global $wpdb;

        $charsetCollate = $wpdb->get_charset_collate();

        $table = $wpdb->prefix . 'rd_plugins';

        if ($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {
            $sql = "CREATE TABLE $table (
                    `id` bigint(20) unsigned not null AUTO_INCREMENT primary key,
                    `name` varchar(100) not null,
                    `slug` varchar(100) not null,
                    `readme_contents` longtext null,
                    `created_at` timestamp default current_timestamp,
                    `updated_at` timestamp null,
                    `deleted_at`timestamp null
            ) $charsetCollate;";
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }
}
