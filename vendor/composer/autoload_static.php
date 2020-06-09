<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitcf48ad9dee10d56f8eed1ce8296ee3d0
{
    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'Tests\\' => 6,
        ),
        'R' => 
        array (
            'ReallySimpleJWT\\' => 16,
        ),
        'B' => 
        array (
            'Benchmarks\\' => 11,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Tests\\' => 
        array (
            0 => __DIR__ . '/..' . '/rbdwllr/reallysimplejwt/tests',
        ),
        'ReallySimpleJWT\\' => 
        array (
            0 => __DIR__ . '/..' . '/rbdwllr/reallysimplejwt/src',
        ),
        'Benchmarks\\' => 
        array (
            0 => __DIR__ . '/..' . '/rbdwllr/reallysimplejwt/benchmarks',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitcf48ad9dee10d56f8eed1ce8296ee3d0::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitcf48ad9dee10d56f8eed1ce8296ee3d0::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
