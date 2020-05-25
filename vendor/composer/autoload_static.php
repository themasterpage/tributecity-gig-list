<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit0c5a02504d44c0b9162a153d2291832c
{
    public static $prefixLengthsPsr4 = array (
        'I' => 
        array (
            'Inc\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Inc\\' => 
        array (
            0 => __DIR__ . '/../..' . '/inc',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit0c5a02504d44c0b9162a153d2291832c::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit0c5a02504d44c0b9162a153d2291832c::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}