<?php

namespace Dev\Test\Inc;

trait RefreshDatabase
{
	private static $tables = [
		'links', 'tags',
		'users', 'usermeta',
		'posts', 'postmeta',
		'terms', 'termmeta',
		'comments', 'commentmeta',
		'term_taxonomy', 'term_relationships',
	];

	private function getNamespace()
	{
		static $ns;

		if (!$ns) {
			$ns = json_decode(
				file_get_contents(
					realpath(__DIR__.'/../../../composer.json')
			))->extra->wpfluent->namespace->current;
		}

		return $ns;
	}

	private function migrator()
	{
		return ($this->getNamespace().'\Database\DBMigrator');
	}

	private function schema()
	{	
		return ($this->getNamespace().'\Framework\Database\Schema');
	}

	public function refreshDatabase($method)
	{
		$schema = $this->schema();

		if ($method === 'migrateUp') {
			static::migrateUpWPTables($schema);
		} else {
			static::migrateDownWPTables($schema);
		}

		$this->migrator()::$method();

		foreach (static::$tables as $table) {
			$schema::truncateTableIfExists($table);
		}
	}

	protected function migrateUpWPTables($schema)
	{
		if (!function_exists('wp_get_db_schema')) {
			require_once(
				ABSPATH . 'wp-admin/includes/schema.php'
			);
		}

		$schema::callDBDelta(wp_get_db_schema());
	}

	protected function migrateDownWPTables($schema)
	{
		foreach ($schema::getTables() as $table) {
			$schema::dropTableIfExists($table);
		}
	}
}
