<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit5817a547620efc0b00a6458011f931d3
{
    public static $prefixLengthsPsr4 = array (
        'C' => 
        array (
            'CILogViewer\\' => 12,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'CILogViewer\\' => 
        array (
            0 => __DIR__ . '/..' . '/seunmatt/codeigniter-log-viewer/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit5817a547620efc0b00a6458011f931d3::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit5817a547620efc0b00a6458011f931d3::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit5817a547620efc0b00a6458011f931d3::$classMap;

        }, null, ClassLoader::class);
    }
}
