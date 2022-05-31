<?php
declare(strict_types=1);
namespace osai;
class SQL_AutoLoader {
    private static string $slash = DIRECTORY_SEPARATOR;
    public static function load_osai(){
        spl_autoload_register(function ($className){
            $location = str_replace('\\',self::$slash, $className);
            $file = str_replace("osai".self::$slash."osai".self::$slash,"osai".self::$slash,__DIR__.self::$slash. $location . '.php');
            if (file_exists($file))
                @include_once $file;
        });
    }
    public static function load_others(?array $directories = null){
        spl_autoload_register(function ($className) use ($directories){
            $location = str_replace('\\',self::$slash, $className);
            if ($directories)
                foreach ($directories as $dir){
                    $dir = rtrim($dir,self::$slash);
                    $dir = ltrim($dir,self::$slash);
                    $file = __DIR__ . self::$slash . $dir . self::$slash . $location . '.php';
                    if (file_exists($file))
                        @include_once $file;
                }
            else{
                $location = str_replace('\\',self::$slash, $className);
                $file = str_replace("osai".self::$slash,"",__DIR__ . self::$slash . $location . '.php');
                if (file_exists($file))
                    @include_once $file;
            }
        });
    }
}
SQL_AutoLoader::load_osai();
# uncomment the line below if you're interested in this package's autoloader
SQL_AutoLoader::load_others();