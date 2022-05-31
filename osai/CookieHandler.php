<?php
declare(strict_types=1);
namespace osai;
final class CookieHandler{
	public function set($cookieName,$cookieValue,$expire="30 days",$path="/") : bool
    {
        $domain = $_SERVER['HTTP_HOST'];
        $secure = true;
		if ($domain == "127.0.0.1" or $domain == "localhost")
		    $secure = false;
		return setcookie($cookieName, $cookieValue, strtotime($expire), $path, $domain, $secure);
	}
	public function destroy($cookieName) : bool
    {
        return $this->set($cookieName,"","now");
    }
}
