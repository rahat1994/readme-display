<?php

namespace Dev\Test\Inc;

class Assert extends \PHPUnit\Framework\Assert
{
	public static function assertThatArrayHasKey($array, $key, $message = '')
	{
	    if (str_contains($key, '*')) {
	        static::hasKeyWithWildcard($key, $array, $message);
	    } else {
	        static::hasKeyWithoutWildcard($key, $array, $message);
	    }
	}

	protected static function hasKeyWithWildcard($key, $array, $message)
	{
	    $parts = explode('.', $key);

	    $currentArray = $array;

	    foreach ($parts as $i => $keyPart) {
	        if ($keyPart === '*') {
	            foreach ($currentArray as $item) {
	                if (!is_array($item)) {
	                    static::fail("Expected an array at level {$i}, but found a non-array item.");
	                }

	                if ($i === count($parts) - 1) {
	                    static::assertArrayHasKey($parts[$i - 1], $item, $message);
	                }
	            }
	            return;
	        } else {
	            if (!isset($currentArray[$keyPart]) || !is_array($currentArray[$keyPart])) {
	                static::fail(
	                	"Invalid array Key '{$keyPart}' or it does not point to an array."
	                );
	            }

	            $currentArray = $currentArray[$keyPart];
	        }
	    }
	}

	protected static function hasKeyWithoutWildcard($key, $array, $message)
	{
	    if (!str_contains($key, '.')) {
	        return static::assertArrayHasKey($key, $array, $message);
	    }

	    $keys = explode('.', $key);
	    $lastKey = array_pop($keys);
	    $currentArray = $array;

	    foreach ($keys as $keyPart) {
	        if (!isset($currentArray[$keyPart]) || !is_array($currentArray[$keyPart])) {
	            static::fail(
	            	"Invalid array Key '{$keyPart}' or it does not point to an array."
	            );
	        }

	        $currentArray = $currentArray[$keyPart];
	    }

	    static::assertArrayHasKey($lastKey, $currentArray, $message);
	}

	public static function assertThatArrayHasValue($a, $k, $v, $m = '')
	{
	    if (str_contains($k, '*')) {
	        static::hasValueWithWildcard($a, $k, $v, $m);
	    } else {
	        static::hasValueWithoutWildcard($a, $k, $v, $m);
	    }
	}

	protected static function hasValueWithoutWildcard($a, $k, $v, $m)
	{
	    if (!str_contains($k, '.')) {
	        return static::assertSame($v, $a[$k], $m);
	    }

	    $keys = explode('.', $k);

	    $lastKey = array_pop($keys);

	    $currentArray = $a;

	    foreach ($keys as $keyPart) {
	        if (
	        	!isset($currentArray[$keyPart]) ||
	        	!is_array($currentArray[$keyPart])
	        ) {
	            static::fail(
	            	"Invalid array Key '{$keyPart}' or it does not point to an array."
	            );
	        }

	        $currentArray = $currentArray[$keyPart];
	    }

	    static::assertSame($v, $currentArray[$lastKey], $m);
	}

	protected static function hasValueWithWildcard($array, $key, $value, $message)
	{
	    $parts = explode('.', $key);

	    $currentArray = $array;

	    foreach ($parts as $i => $keyPart) {
	        if ($keyPart === '*') {
	            foreach ($currentArray as $item) {
	                if (!is_array($item)) {
	                    static::fail(
	                    	"Expected an array at level {$i}, but found a non-array item."
	                    );
	                }

	                if ($i === count($parts) - 1) {
	                    static::assertSame($value, $item, $message);
	                } else {
	                    $remainingKey = implode(
	                    	'.', array_slice($parts, $i + 1)
	                    );
	                    
	                    static::hasValueWithoutWildcard(
	                    	$item, $remainingKey, $value, $message
	                    );
	                }
	            }
	            return;
	        }

	        if (!array_key_exists($keyPart, $currentArray)) {
	            static::fail(
	            	"Invalid array key '{$keyPart}' does not exist."
	            );
	        }

	        $currentArray = $currentArray[$keyPart];
	    }

	    static::assertSame($value, $currentArray, $message);
	}
}
