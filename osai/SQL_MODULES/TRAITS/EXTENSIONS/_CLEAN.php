<?php
declare(strict_types=1);
namespace osai\SQL_MODULES\TRAITS\EXTENSIONS;

/**
 * Trait SQL_EXTENSION_CLEAN
 * @package osai\SQL_MODEL\EXTENSIONS
 * @modified 04/06/2021
 */
trait _CLEAN {

    protected static array $stock_escape_string = ["%3D","%21","%2B","%40","%23","%24","%25","%5E","%26","%2A","%28","%29","%27",
        "%22","%3C", "%3E","%3F","%2F","%5C","%7C","%60","%2C","_","-","–","%0A"];
    protected static array $escape_string = [];
    /**
     * CLEAN variables for SQL or generally
     * @param string|int $value string value to be cleansed
     * @param int $level__combo <table><tr><th>BASE FUNCTIONS</th></tr>
     * <tr><td>0</td><td>real_escape_string[<b>default</b>]</td></tr><tr><td>1</td><td>strip_tags</td></tr>
     * <tr><td>2</td><td>trim</td></tr><tr><td>3</td><td>htmlspecialchars</td></tr>
     * <tr><td>4</td><td>rawurlencode</td></tr><tr><td>5</td><td>str_replace</td></tr>
     * <tr><td>6</td><td>url_beautify</td></tr>
     * <tr><th>LEVEL MODE</th></tr>
     * <tr><td>Double</td><td>[10=1 & 0] [11= 2 & 0] [12=0 & 3] [13=3 & 1] [14=1 & 2] [15=3 & 2]</td></tr>
     * <tr><td>Multiple</td><td>[16=1 & 2 & 0] [17=3 & 2 & 0] [18=1 && 3 && 0] [19=1 && 3 && 2] [20=1,3,2,0]</td></tr>
     * <tr><th>COMBO MODE</th><td>Dev has the freedom of combining cleansing independently, but each number must appear once
     * and cleansing happens from left-to-right</td></tr>
     * </table>
     * @param array $options Optional settings required by cleansing agents and to also change cleansing core<br>
     * <em>['flags | flag' => ENT_QUOTES,'allowed | tags | allowed_tags'=> '<br><div>','core'=>"combo" | 'combo'=>1]</em>
     * <em>Passing "combo" as a value will work in place of the "core" key</em>
     * <div><b>As of 2.0.1, the `clean` function ignores passing empty value to the "value" argument except "strict" is
     * passed in the option array as a value</b></div>
     * pass an int value of 1 to the function to debug it
     * @return mixed
     */
    public function clean($value, int $level__combo = 0, ...$options) {
        // perquisite
        $core = self::core();
        $link = $core->get_link();
        
        $options = $core->array_flatten($options);
        $flags = $options['flag'] ?? $options['flags'] ?? ENT_QUOTES;
        $allowedTags = $options['allowed'] ?? $options['tags'] ?? $options['allowed_tags'] ?? "";
        // this condition is meant for the $find variable when handling url_beautify
        if(count(self::$escape_string) == 0) self::$escape_string = self::$stock_escape_string;
        if($level__combo == 6 && !in_array('ignore_preset',$options)){
            $this->reset_escape_string();
            $this->add_escape_string("/","\\","\"","#","|","^","*","~","!","$","@","%","`",';', ':', '=','<',
                '>',"»"," ","%20","?","'",'"',"(",")","[","]",".",",");
        }
        $find = $options['search'] ?? $options['find'] ?? self::$escape_string;
        $replace = $options['replace'] ?? $options['put'] ?? "";
        
        $esc_str = function ($mode,$value) use($link){
            // Extra layer of security for escape string
            if($mode == "strict") {
                $keyWords = ["SELECT","INSERT","DELETE","UPDATE","CREATE","DROP","SHOW","USE","DESCRIBE","DESC","ALTER"];
                $keyWords = array_merge($keyWords, array_map("strtolower", $keyWords));
                $value = str_replace($keyWords, array_map(fn($x) => mysqli_real_escape_string($link,$x), $keyWords), $value);
            }

            return mysqli_real_escape_string($link,$value);
        };
        // core && difficulty
        if(in_array("combo",$options)) $mode = "combo";
        else $mode = $options['core'] ?? "level";
        if(in_array("strict",$options)) $difficulty = "strict";
        else $difficulty = "loose";
        // debug
        if(in_array(1,$options)) $this->show_exception(3,[$value,$level__combo,$options]);
        // check mate
        if (empty($value) && $difficulty == "strict") $this->show_exception(1);
        elseif (empty($value) && $difficulty == "loose") return $value;
        if(!is_string($value) && !is_int($value)) $this->show_exception(2, [$value]);
        // function
        $func = [
            /*0*/ fn ($val=null) => $esc_str($difficulty,$val ?? $value),
            /*1*/ fn ($val=null) => strip_tags((string) ($val ?? $value),$allowedTags),
            /*2*/ fn ($val=null) => trim($val ?? $value),
            /*3*/ fn ($val=null) => htmlspecialchars($val ?? $value,$flags),
            /*4*/ fn ($val=null) => rawurlencode($val ?? $value),
            /*5*/ fn ($val=null) => str_replace($find,$replace,$val ?? $value),
            /*6*/ fn ($val=null) => strtolower(preg_replace("/--+/","-", str_replace($find,"-",trim($val ?? $value))))
        ];
        $permute = function ($combination,$value) use ($func) {
            foreach ($combination as $combo) { $value = $func[$combo]($value); } return $value;
        };
        // cleansing
        if(isset($options['combo']) OR $mode == "combo") {
            if (extension_loaded('mbstring'))
                $combine = mb_str_split("$level__combo");
            else
                $combine = str_split("$level__combo");
            if (count($combine) !== count(array_unique($combine))) $this->show_exception(1.5, [1 => $level__combo]);
            $value = $permute($combine,$value);
        }
        else{
            if(($level__combo + 1) > count($func)) {
                switch ($level__combo){
                    case 10: $combine = [1,0]; break;
                    case 11: $combine = [2,0]; break;
                    case 12: $combine = [0,3]; break;
                    case 13: $combine = [3,1]; break;
                    case 14: $combine = [1,2]; break;
                    case 15: $combine = [3,2]; break;
                    case 16: $combine = [1,2,0]; break;
                    case 17: $combine = [3,2,0]; break;
                    case 18: $combine = [1,3,0]; break;
                    case 19: $combine = [1,3,2]; break;
                    case 20: $combine = [1,3,2,0]; break;
                    default: $this->show_exception(1.5, [1 => $level__combo]); break;}
                $value = $permute($combine,$value);
            }
            else $value = $func[$level__combo]();
        }
        return $value;
    }
    public function clean_multi(int $level,...$values) : array {
        $return = [];
        for ($i = 0; $i < count($values); $i++){
            array_push($return,$this->clean($values[$i],$level));
        }
        return $return;
    }
    public function add_escape_string(...$escape_string) : void {
        if(count(self::$escape_string) == 0) self::$escape_string = self::$stock_escape_string;
        self::$escape_string = array_merge(self::$escape_string, self::core()->array_flatten($escape_string));
    }
    public function get_escape_string() : array { return self::$escape_string; }
    public function reset_escape_string() : void { self::$escape_string = self::$stock_escape_string;}
}
