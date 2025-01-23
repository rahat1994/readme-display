<?php

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/../../vendor/autoload.php';

if (!function_exists('get_methods')) {
	function get_methods($class) {
		$methods = [];
	
		$object = new \ReflectionClass($class);

		foreach ($object->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
			
			$name = $method->getName();
			
			if (!str_starts_with($name, '_')) {
				
				$params = array_map(function($p) {
					return '$'.$p->name;
				}, $method->getParameters());
				
				$doc = $method->getDocComment();

				$reflectionMethod = new \ReflectionMethod($class, $name);
				$fnDec = 'public function';
				if ($reflectionMethod->isStatic()) {
					$fnDec = 'public static function';
				}

				$header = "$fnDec $name(". implode(', ', $params) .")";

				$methods[$name] = implode(
					PHP_EOL, array_merge(parse_docblock($doc), [$header])
				);
			}
		}

		return $methods;
	}
}

if (!function_exists('parse_docblock')) {
	function parse_docblock($sourceCode) {
	    $pattern = '/\/\*\*(.*?)\*\//s';

	    if (!preg_match($pattern, $sourceCode, $matches)) {
	    	return [];
	    }
	    
	    $cleanedDocBlock = array_map(function($line) {
	        return trim(ltrim($line, ' *'));
	    }, explode("\n", $matches[1]));

	    return array_filter($cleanedDocBlock);
	}
}

if (!function_exists('getClassesFromDirectory')) {
    function getClassesFromDirectory($directory) {
        $classes = [];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
            	$directory, \RecursiveDirectoryIterator::SKIP_DOTS
            ),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                
                $filePath = $file->getRealPath();
                
                $content = file_get_contents($filePath);

                $isClass = false;
                foreach (token_get_all($content) as $token) {
                	if (is_array($token) && isset($token[0])) {
                		if ($token[0] === T_CLASS) {
                			$isClass = true;
                			break;
                		}
                		continue;
                	}
                }
                
                if (!$isClass) continue;

                preg_match(
                    '/\bnamespace\s+([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff\\\\]*)\s*;/',
                    $content,
                    $namespaceMatches
                );

                preg_match_all(
                    '/\bclass\s+([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)/', $content, $classMatches
                );


                if (!empty($namespaceMatches)) {
                    $namespace = trim($namespaceMatches[1]);

                    foreach ($classMatches[1] as $className) {
                    	$className = ucfirst($className);
                        $fqn = empty($namespace) ? $className : $namespace . '\\' . $className;
                        if (class_exists($fqn)) {
                            $reflection = new \ReflectionClass($fqn);
                            if (!$reflection->isSubclassOf(\Exception::class)) {
                                if (
                                	$reflection->isUserDefined() &&
                                	$reflection->isInstantiable()
                                ) {
                                    $classes[] = $fqn;
                                }
                            }
                        }
                    }
                }
            }
        }

        return array_unique($classes);
    }
}

if (!function_exists('recursiveCopy')) {
	function recursiveCopy($source, $dest, $excludeList) {
	    if (!is_dir($dest)) {
	        mkdir($dest);
	    }

	    $files = scandir($source);
	    foreach ($files as $file) {
	        if ($file != "." && $file != ".." && !in_array($file, $excludeList)) {
	            $sourcePath = "{$source}/{$file}";
	            $destPath = "{$dest}/{$file}";

	            if (is_dir($sourcePath)) {
	                recursiveCopy($sourcePath, $destPath, $excludeList);
	            } else {
	                copy($sourcePath, $destPath);
	            }
	        }
	    }
	}
}

if (!function_exists('createZipArchive')) {
	function createZipArchive($source, $destination) {
	    $zip = new \ZipArchive();
	    if ($zip->open($destination, \ZipArchive::CREATE) !== TRUE) {
	        exit("Cannot create a zip archive");
	    }

	    $files = new \RecursiveIteratorIterator(
	        new \RecursiveDirectoryIterator($source),
	        \RecursiveIteratorIterator::LEAVES_ONLY
	    );

	    foreach ($files as $name => $file) {
	        if (!$file->isDir()) {
	            $filePath = $file->getRealPath();
	            $relativePath = substr($filePath, strlen($source) + 1);

	            $zip->addFile($filePath, $relativePath);
	        }
	    }

	    $zip->close();
	}
}

if (!function_exists('deleteDirectory')) {
	function deleteDirectory($dir) {
	    if (is_dir($dir)) {
	        $objects = scandir($dir);
	        foreach ($objects as $object) {
	            if ($object != "." && $object != "..") {
	                if (is_dir($dir . "/" . $object)) {
	                    deleteDirectory($dir . "/" . $object);
	                } else {
	                    unlink($dir . "/" . $object);
	                }
	            }
	        }
	        rmdir($dir);
	    }
	}
}