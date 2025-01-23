<?php require __DIR__.'/../../../vendor/autoload.php';

echo "Please wait a few moments..." . PHP_EOL;

if (!function_exists('dd')) {
    function dd(/*args*/) {
        ob_start();
        foreach (func_get_args() as $arg) {
            echo "<pre>";
            print_r($arg);
            echo "</pre>";
        }
        $ret = ob_get_clean();

        if (str_contains(strtolower(php_sapi_name()), 'cli')) {
            echo PHP_EOL.PHP_EOL . strip_tags($ret) . PHP_EOL.PHP_EOL;
        } else {
            echo strip_tags($ret);
        }
        die;
    }
}

use PhpParser\Error;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use PhpParser\NodeVisitor\NameResolver;

$classData = [
	'props' => [],
	'methods' => [],
	'className' => '',
];

$classSourceCode = file_get_contents(
	$filePath = __DIR__.'/../../../../vendor/composer/autoload_static.php'
);

$parser = (new ParserFactory)->createForNewestSupportedVersion();
$traverser = new NodeTraverser();
$traverser->addVisitor(new NameResolver());

try {
	$ns = json_decode(
		file_get_contents(__DIR__.'/../../../../composer.json'), true
	)['extra']['wpfluent']['namespace']['current'];

    $stmts = $parser->parse($classSourceCode);

    $methodVisitor = new class (
    	$ns, $filePath, $classData
    ) extends \PhpParser\NodeVisitorAbstract {
    	private $ns = null;
    	private $filePath = null;
    	private $classData = null;
    	public function __construct($ns, $filePath, &$classData) {
    		$this->ns = $ns;
    		$this->filePath = $filePath;
    		$this->classData = &$classData;
    	}
        public function enterNode(Node $node) {
        	if ($node instanceof Node\Stmt\Class_) {
        		$this->classData['className'] = $node->name->name;
        	} elseif ($node instanceof Node\Stmt\ClassMethod) {
                $this->classData['methods'][] = $this->getFormattedCode($node);
            } elseif ($node instanceof Node\Stmt\Property) {
                $propertyName = $node->props[0]->name->name;
                $propertyType = $node->isStatic() ? 'static' : '';
                $visibility = $this->getPropertyVisibility($node);
                $defaultValue = $this->getPropertyDefaultValue($node);
                
                eval('$defaultValue = ' . $defaultValue . ';');

                if ($propertyName === 'prefixDirsPsr4') {
                	$defaultValue = array_filter($defaultValue, function($key) {
                		return strpos($key, $this->ns.'\\') === 0;
                	}, ARRAY_FILTER_USE_KEY);
                }

                $this->classData['props'][$propertyName] = [
                	'visibility' => $visibility,
                	'propertyType' => $propertyType,
                	'propertyName' => $propertyName,
                	'defaultValue' => $defaultValue,
                ];
            }
        }

        private function getFormattedCode(Node $node): string {
            $startPos = $node->getAttribute('startFilePos');
            $endPos = $node->getAttribute('endFilePos');
            $content = file_get_contents($this->filePath);
            return $startPos !== null && $endPos !== null
                ? substr($content, $startPos, $endPos - ($startPos-1))
                : '';
        }

        private function getPropertyVisibility(Node\Stmt\Property $property): string {
            if ($property->isPublic()) {
                return 'public';
            } elseif ($property->isProtected()) {
                return 'protected';
            } elseif ($property->isPrivate()) {
                return 'private';
            } else {
                return 'unknown';
            }
        }

        private function getPropertyDefaultValue(Node\Stmt\Property $property): string {
            if (isset($property->props[0]->default)) {
                return $this->getFormattedCode($property->props[0]->default);
            }
        }
    };

    $traverser->addVisitor($methodVisitor);
    $traverser->traverse($stmts);
    
} catch (Error $error) {
    echo 'Parse Error: ', $error->getMessage();
}

(function($path) use ($ns, $classData) {
	$clsName = $classData['className'];
	$props = $classData['props'];
	$methods = $classData['methods'];
	$filesArray = $props['files'];
	$prefixDirsPsr4Array = $props['prefixDirsPsr4'];
	$prefixLengthsPsr4Array = $props['prefixLengthsPsr4'];
	$classMapArray = $props['classMap'];

	$filesEntry = "{$filesArray['visibility']} {$filesArray['propertyType']} \${$filesArray['propertyName']}";
	$filesArray = var_export($filesArray['defaultValue'], 1);

	$prefixDirsPsr4Entry = "{$prefixDirsPsr4Array['visibility']} {$prefixDirsPsr4Array['propertyType']} \${$prefixDirsPsr4Array['propertyName']}";
	$prefixDirsPsr4Array = var_export($prefixDirsPsr4Array['defaultValue'], 1);

	$prefixLengthsPsr4Entry = "{$prefixLengthsPsr4Array['visibility']} {$prefixLengthsPsr4Array['propertyType']} \${$prefixLengthsPsr4Array['propertyName']}";
	$prefixLengthsPsr4Array = var_export($prefixLengthsPsr4Array['defaultValue'], 1);

	$foundKey = null;
	eval('$arr = ' . $prefixLengthsPsr4Array . ';');
	foreach ($arr as $key => $value) {
		foreach ($value as $k => $v) {
			if (str_starts_with($k, $ns)) {
				$foundKey = $key;
				$prefixLengthsPsr4Array = $arr[$key];
			}
		}
	}
	
	foreach ($prefixLengthsPsr4Array as $k => $v) {
		if (!str_starts_with($k, $ns)) {
			unset($prefixLengthsPsr4Array[$k]);
		}
	}

	$prefixLengthsPsr4Array = [$foundKey => $prefixLengthsPsr4Array];
	$prefixLengthsPsr4Array = var_export($prefixLengthsPsr4Array, 1);

	$classMapEntry = "{$classMapArray['visibility']} {$classMapArray['propertyType']} \${$classMapArray['propertyName']}";
	$classMapArray = var_export($classMapArray['defaultValue'], 1);

	$methodCode = '';
	foreach ($methods as $method) {
		$methodCode .= $method.PHP_EOL;
	}
	
	$classCode = <<<CODE
	<?php

	namespace Composer\Autoload;

	class $clsName
	{
		$filesEntry = $filesArray;
		$prefixDirsPsr4Entry = $prefixDirsPsr4Array;
		$prefixLengthsPsr4Entry = $prefixLengthsPsr4Array;
		$classMapEntry = $classMapArray;

		$methodCode
	}
	CODE;

	$classCode = str_replace(__DIR__, '__DIR__.\'', $classCode);
	$classCode = str_replace("'__DIR__", "__DIR__", $classCode);
	file_put_contents($path, $classCode);
})($filePath);
