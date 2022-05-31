<?php
declare(strict_types=1);
namespace osai\SQL_MODULES\TRAITS\EXTENSIONS;

/**
 * MULTI_QUERY
 * @dependencies osai::SQL_EXTENSIONS::RESULT_GEN
 * @modified 15/02/2021
 */
trait _MULTI_QUERY {
    /**
     * @param string $query
     * @param int $debug
     * @return bool
     */
    public function query_multi(string $query, int $debug = 0) : bool {
        self::core()->query="<div>$query</div>";
        $option['debug'] = [$query,"MULTI"];
        $run = false;
        $link = self::core()->get_link();

        if($debug) $this->show_exception(-1,$option['debug']);
        if (@mysqli_multi_query($link,$query)) {
            do {
                if ($run = mysqli_store_result($link)) {
                    $this->ext("result_gen")->loop_row($run);
                    mysqli_free_result($run);
                }
            } while (mysqli_next_result($link));
            $run = true;
        } else $this->show_exception(0,$option['debug']);
        return $run;
    }
}