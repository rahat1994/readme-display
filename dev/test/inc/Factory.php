<?php

namespace Dev\Test\Inc;

use Exception;
use WP_UnitTest_Factory;

class Factory
{
	public function __get($key)
	{
		return $this->resolveCustomFactory($key);
	}

	protected function resolveCustomFactory($key)
	{
		$keyName = strtoupper($key[0]).substr($key, 1);

		$file = realpath(__DIR__.'/../../factories/'.$keyName.'Factory.php');
		
		if (file_exists($file)) {
			$class = '\\Dev\\Factories\\'.basename($file, '.php');
			if (!class_exists($class)) {
				require $file;
			}

			return new $class;
		}

		$cls = __CLASS__;
		throw new Exception("Undefined property {$cls}::{$key}.");
	}
}
