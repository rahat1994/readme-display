<?php

class BuildCommand
{
	private $args = [];

	public function __construct($args)
	{
		$this->args = $args;
	}

	public function run()
	{
		$pluginDir = $this->args['cwd'];
		
		$tempDir = $pluginDir.'/buildtemp';
		
		$zipFile = $pluginDir . '/' . basename($pluginDir) . '.zip';
		
		$excludeList = [
		    '.git',
		    '.gitignore',
		    '.gitmodules',
		    'README.md',
		    'composer.json',
		    'composer.lock',
		    'package.json',
		    'package-lock.json',
		    'dev',
		    'buildtemp',
		    'node_modules',
		    'webpack.mix.js',
		    'wp-tests-config.php',
		    'wpf',
		    'wpflog',
		];

		if (!is_dir($tempDir)) mkdir($tempDir);

		recursiveCopy($pluginDir, $tempDir, $excludeList);

		createZipArchive($tempDir, $zipFile);

		deleteDirectory($tempDir);
		
		echo "The zip file has been create at: {$zipFile}".PHP_EOL;
	}
}
