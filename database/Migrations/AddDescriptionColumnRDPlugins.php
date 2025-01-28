<?php
// phpcs:disable
namespace ReadmeDisplay\Database\Migrations;

class AddDescriptionColumnRDPlugins
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

        if ($wpdb->get_var("SHOW TABLES LIKE '$table'") === $table) {

            $isMigrated = $wpdb->get_col($wpdb->prepare("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND COLUMN_NAME='description' AND TABLE_NAME=%s", $table));
            if (!$isMigrated) {
                // $wpdb->query("ALTER TABLE {$table} CHANGE `key` `meta_key` varchar(100) NOT NULL AFTER `object_id`");

                $sql = "ALTER TABLE {$table} ADD `description` TEXT NULL AFTER `slug`";

                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                dbDelta($sql);
            }
        }
    }
}
