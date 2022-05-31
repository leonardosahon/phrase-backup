<?php
declare(strict_types=1);
namespace osai\SQL_MODULES\TRAITS;
use mysqli;
/**
 * Trait SQL_CONFIG
 * @package osai\SQL_MODEL
 * @modified 16/02/2021
 */
trait SQL_CONFIG{
    private static mysqli $link;
    private static string $CHARSET = "utf8mb4";

    public function _init($connection): void { $this->___init($connection);}
    private function ___init($connection): void {
        // confirm development environment or guess it based on host
        if (@empty($connection['env']) && ($_SERVER['HTTP_HOST'] == "127.0.0.1" || $_SERVER['HTTP_HOST'] == "localhost"))
            $this->set_env("DEVELOPMENT");
        else
            $this->set_env("PRODUCTION");

        if(is_array($connection)) $this->connect($connection);
        else $this->plug($connection);
    }

    /**
     * Connect Controller Manually From Here
     * @param $cnn_arg array associative array of connection parameter ["host","user","password","db","env"]
     * env takes either ("dev" || "prod") || ("development" || "production")
     * @return mysqli
     **/
    public function connect(array $cnn_arg) : mysqli {
        $host = $cnn_arg['host'];
        $usr = $cnn_arg['user'];
        $pass = $cnn_arg['password'];
        $dbname = $cnn_arg['db'];
        $charset = $cnn_arg['charset'] ?? self::$CHARSET;
        $this->set_env($cnn_arg['env'] ?? $this->get_env());
        $cxn = $this->ping(true,null, true);
        if(!($cxn['host'] == $host and $cxn['user'] == $usr and $cxn['db'] == $dbname)) {
            if ($x = @mysqli_connect($host, $usr, $pass, $dbname)){
                $x->set_charset($charset);
                $this->set_link($x);
            }
            else $this->show_exception(6);
        }
        return $this->get_link();
    }

    /**
     * Connect Controller Using Existing Link
     * @param mysqli $link
     * @return mysqli
     */
    public function plug(mysqli $link) : mysqli {
        $cxnOld = $this->ping(true);
        if(empty($cxnOld['host']) || empty($cxnOld['user']) || empty($cxnOld['db']))
            $this->set_link($link);
        else {
            $cxnNew = $this->ping(true, $link);
            if (!($cxnOld['host'] == $cxnNew['host'] and $cxnOld['user'] == $cxnNew['user'] and $cxnOld['db'] == $cxnNew['db']))
                $this->set_link($link);
        }
        return $this->get_link();
    }

    # close connection
    public function close(?mysqli $link = null, bool $silent_error = false) : bool {
        if(@mysqli_close($link ?? $this->get_link())) return true;
        if($silent_error == false) $this->show_exception(7);
        return false;
    }

    /**
     * Check Database Connection
     * @param bool $ignore_msg false by default to echo connection info
     * @param mysqli|null $link link to database connection
     * @param bool $ignore_no_conn false by default to silence no connection error
     * @return array containing [host,user,db]
     **/
    public function ping(bool $ignore_msg = false, ?mysqli $link = null, bool $ignore_no_conn = false) : array {
        $cxn = $link ?? $this->get_link() ?? null; $db = ""; $usr = ""; $host = "";
        if($cxn){
            if(isset($this->get_link()->host_info)) {
                if (@mysqli_ping($cxn)) {
                    $x = $this->query("SELECT SUBSTRING_INDEX(host, ':', 1) AS host_short,
                    USER AS users, db FROM information_schema.processlist", "assoc", "select");
                    $db = $x['db'];
                    $usr = $x['users'];
                    $host = $x['host_short'];
                    if ($ignore_msg == false) $this->show_exception(5, [$db, $usr, $host]);
                }
                else if ($ignore_no_conn == false) $this->show_exception(4);
            }
        } return ["host" => $host, "user" => $usr, "db" => $db];
    }

    public function set_link(mysqli $link): void { self::$link = $link;}

    public function get_link(): ?mysqli { return self::$link ?? null; }
}