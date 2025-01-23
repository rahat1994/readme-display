<?php

namespace ReadmeDisplay\Database;

use ReadmeDisplay\Framework\Database\Schema;

class DBMigrator
{
    private static $migrations = [
        // ...
    ];

    public static function run($network_wide = false)
    {
        return static::migrateUp($network_wide);
    }

    public static function migrateUp($network_wide = false)
    {
        /**
         * The migration method uses deDelta function and
         * the dbDelta is able to detect changes and can
         * modify the only changes made in the column
         * type/size and added new columns but it
         * can't drop a column from the table.
         * So, use Schema::alterTableIfExists
         * method when you need more changes
         * than dbDelta can handle or use
         * Schema::updateTable method.
         * 
         * @see https://codex.wordpress.org/Creating_Tables_with_Plugins#Creating_or_Updating_the_Table
         */
        return Schema::migrate(static::getMigrations());
    }

    public static function getMigrations()
    {
        $sqls = array_merge(
            static::getMigratableFiless(
                glob(__DIR__ . '/Migrations/*.sql')
            ),
            static::mapMigrations()
        );

        return array_filter($sqls, function ($file) {
            return basename($file) !== 'example.sql';
        });
    }

    public static function getMigratableFiless($files)
    {
        $result = [];

        $files = array_map(function ($filePath) {
            return [
                'path' => $filePath,
                'ctime' => filectime($filePath),
                'mtime' => filemtime($filePath)
            ];
        }, $files);

        usort($result, function ($file1, $file2) {
            if ($file1['ctime'] == $file2['ctime']) {
                return $file1['mtime'] - $file2['mtime'];
            }
            return $file1['ctime'] - $file2['ctime'];
        });

        foreach (array_reverse($files) as $file) {
            $result[basename($file['path'], '.sql')] = $file['path'];
        }

        return array_filter($result, function ($f) {
            return filesize($f) > 0;
        });
    }

    protected static function mapMigrations()
    {
        $result = [];

        foreach (static::$migrations as $key => $value) {
            if (is_int($key)) {
                $file = basename($value);
                $key = substr($file, 0, strpos($file, '.'));
            }

            if (@filesize($value)) {
                $result[$key] = $value;
            }
        }

        return array_filter($result);
    }

    public static function migrateDown()
    {
        foreach (array_keys(static::getMigrations()) as $table) {
            Schema::dropTableIfExists($table);
        }
    }
}
