<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitd122c607ddc2cd5ae6823183915b7732
{
    public static $prefixLengthsPsr4 = array (
        'b' => 
        array (
            'barkgj\\functions-library\\' => 25,
            'barkgj\\datasink\\' => 16,
            'barkgj\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'barkgj\\functions-library\\' => 
        array (
            0 => __DIR__ . '/..' . '/barkgj/datasink-library/src',
        ),
        'barkgj\\datasink\\' => 
        array (
            0 => __DIR__ . '/..' . '/barkgj/datasink-library',
            1 => __DIR__ . '/..' . '/barkgj/tasks-library/src/vendor/barkgj/datasink-library',
        ),
        'barkgj\\' => 
        array (
            0 => __DIR__ . '/..' . '/barkgj/functions-library/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitd122c607ddc2cd5ae6823183915b7732::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitd122c607ddc2cd5ae6823183915b7732::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitd122c607ddc2cd5ae6823183915b7732::$classMap;

        }, null, ClassLoader::class);
    }
}
