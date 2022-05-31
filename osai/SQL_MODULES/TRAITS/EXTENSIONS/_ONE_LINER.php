<?php
declare(strict_types=1);
namespace osai\SQL_MODULES\TRAITS\EXTENSIONS;

trait _ONE_LINER {
    public function change_db(string $dbName) : bool {
        return mysqli_select_db(self::core()->get_link(),$this->ext("clean")->clean($dbName,20));
    }
    public function last_id(){
        return mysqli_insert_id(self::core()->get_link());
    }
    /**
     * @param bool $strict [default = true] throws error if nothing is found in request POST request
     * @return object
     */
    public function get_json(bool $strict = true) : object {
        $x = file_get_contents("php://input");
        $msg = "No values found in request; check if you actually sent your values as \$_POST";
        if(!empty($x) && substr($x,0,1) != "{") {
            $x = "";
            $msg = "JSON formatted \$_POST needed; but invalid JSON format was found";
        }
        if($strict && empty($x)) $this->show_exception(9,[
            "title" => "Get JSON Error [SQE::9::EXT::ONE_LINER::get_json]",
            "body" => "<div style='color: #eeb300; font-weight: bold; margin: 5px 1px;'>$msg</div>"
        ]);
        return json_decode($x);
    }
    public function to_object(array $array) : object {
        $obj = new \stdClass();

        foreach ($array as $k => $v){
            if(is_array($v)) {
                $obj->{$k} = $this->to_object($v);
                continue;
            }
            $obj->{$k} = $v;
        }

        return $obj;
    }
    /**
     * @param $cols string columns to extract
     * @param $table string table to extract from
     * @param string $where default[id] auto incremented column
     * @param int $debug 1 if you wish to debug your query
     * @return array|null
     **/
    public function last_col(string $cols, string $table, string $where = 'id', int $debug = 0) : ?array {
        $id = ($where=='id') ? ("id=" . $this->last_id()) : ($where);
        return self::core()->query("SELECT $cols FROM $table WHERE $id", "last_insert", $debug);
    }
    /**
     * Get last value of a table's int column
     * @param string $table
     * @param string $column column to check for last value
     * @param string|null $clause condition for selection
     * @return int
     */
    public function last_value(string $table, string $column = "id", ?string $clause = null) : int {
        $clause = $clause ?? "ORDER BY $column DESC LIMIT 1";
        if(empty($column)) $this->use_exception("Query Execution Error",
            "You need to specify a column to check for last_value");
        return (int) (self::core()->get($column,$table,$clause,'row','!')[0] ?? 0);
    }
    public function date_now(?string $datetime = null, int $level = 10, string $format = "Y-m-d H:i:s") : string {
        $datetime = $datetime ?? date("Y-m-d H:i:s");
        switch ($level){
            case 0: $format = "H:i:s"; break;
            case 1: $format = "Y-m-d"; break;
            case 2: $format = "D d, M Y | h:i a"; break;
            default: break;
        }
        return date($format, strtotime($datetime));
    }
}
