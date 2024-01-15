<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit2b7aa11d88b32870484ed1416629a2ec
{
    public static $files = array (
        '9b38cf48e83f5d8f60375221cd213eee' => __DIR__ . '/..' . '/phpstan/phpstan/bootstrap.php',
        '38143a9afc50997d55e4815db8489d1c' => __DIR__ . '/..' . '/rector/rector/bootstrap.php',
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInit2b7aa11d88b32870484ed1416629a2ec::$classMap;

        }, null, ClassLoader::class);
    }
}
