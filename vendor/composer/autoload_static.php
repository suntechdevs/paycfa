<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitb105e88e03d8d87214c2e8c120911d07
{
    public static $prefixLengthsPsr4 = array (
        's' => 
        array (
            'suntech\\HelloWorld\\' => 19,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'suntech\\HelloWorld\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitb105e88e03d8d87214c2e8c120911d07::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitb105e88e03d8d87214c2e8c120911d07::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
