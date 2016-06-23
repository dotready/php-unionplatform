<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitec98d6871f0b488b9b35a29da548430e
{
    public static $prefixLengthsPsr4 = array (
        'p' => 
        array (
            'phpunionplatform\\' => 17,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'phpunionplatform\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/phpunionplatform',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitec98d6871f0b488b9b35a29da548430e::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitec98d6871f0b488b9b35a29da548430e::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
