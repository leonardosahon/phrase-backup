<?php
declare(strict_types=1);
namespace osai;
include_once "SQL_AutoLoader.php";
use mysqli;
use osai\SQL_MODULES\CLASSES\{_SQL_CORE,
    EXTENSIONS\_CLEAN,
    EXTENSIONS\_ID_GEN,
    EXTENSIONS\_MULTI_QUERY,
    EXTENSIONS\_ONE_LINER,
    EXTENSIONS\_RESULT_GEN};
use osai\SQL_MODULES\TRAITS\SQL_EXCEPTIONS;

/**
 * Simple Query Language
 * @since 23/11/2019
 * @author Osahenrumwen Leonard Aigbogun
 * @modified 22/05/2021
 * @version 2.0.0
 * @dependencies PHP 7.4^
 * @package osai
 **/
class SQL{
    use SQL_EXCEPTIONS;
    public function __construct(?string $environment = null){
        if($environment)
            $this->set_env($environment);
    }

    /**
     * @param $connection mysqli|array|null The link to a mysqli connection or an array of [host, user, password, db]
     * When nothing is passed the class assumes dev isn't doing any db operation
     */
    public static function init($connection = null): _SQL_CORE {
        $core = self::core();
        $core->_init($connection);
        return $core;
    }

    public static function core(): _SQL_CORE {
        return new _SQL_CORE();
    }

    // this is necessary if dev wants to use just one extension instead of the whole class
    public function ext(string $extension) {
        switch (strtolower($extension)) {
            case "clean" : $rtn = new _CLEAN(); break;
            case "id_gen": $rtn = new _ID_GEN(); break;
            case "one_liner": $rtn =  new _ONE_LINER(); break;
            case "multi_query": $rtn =  new _MULTI_QUERY(); break;
            case "result_gen" : $rtn =  new _RESULT_GEN(); break;
            default: return null;
        }

        return $rtn;
    }
}