<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInitbfe2663f2b125dbfbbf8bb37511e87fb
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        require __DIR__ . '/platform_check.php';

        spl_autoload_register(array('ComposerAutoloaderInitbfe2663f2b125dbfbbf8bb37511e87fb', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInitbfe2663f2b125dbfbbf8bb37511e87fb', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInitbfe2663f2b125dbfbbf8bb37511e87fb::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
