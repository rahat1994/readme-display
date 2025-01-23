<?php

$classList = getClassesFromDirectory(
    __DIR__.'/../../vendor/wpfluent/framework/src/WPFluent'
);

$classesArray = [];

foreach ($classList as $class) {
    $pieces = explode('\\', $class);
    $classesArray[$class] = end($pieces);
}

$classesArray = array_flip($classesArray);

uksort($classesArray, function($a, $b) {
    return strcmp($a, $b);
});

return array_flip($classesArray);