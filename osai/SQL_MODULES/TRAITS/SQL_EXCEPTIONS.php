<?php
declare(strict_types=1);
namespace osai\SQL_MODULES\TRAITS;
/**
 * Trait SQL_EXCEPTIONS
 * @package osai\SQL_MODEL
 * @modified 16/02/2021
 */
trait SQL_EXCEPTIONS {
    private static string $ENV = "";
    public function set_env(string $ENV): void {
        $ENV = strtolower($ENV);
        if($ENV == "dev" ?? "development") $ENV = "development";
        else $ENV = "production";

        self::$ENV = strtoupper($ENV);
    }

    public function get_env(): string { return self::$ENV; }

    public function use_exception(string $title, string $body, bool $kill = true) : void {
        $this->show_exception(9,["title" => $title, "body" => $body, "kill" => $kill ]);
    }

    private function container ($title,$body,$other=[]) : string {
        if($other['core'] == "error") {$title_color = "#ff0014"; $body_color = "#ff5000";}
        elseif($other['core'] == "success") {$title_color = "#1cff03"; $body_color = "#1b8b07";}
        else{$title_color = "#5656f5"; $body_color = "#dea303";}
        $env = $this->get_env();
        if(isset($other['raw_value']))
            $body = $this->convertRaw($other['raw_value'],"#65fad8","__RAW_VALUE__",$body);
        if(isset($other['raw_type']))
            $body = $this->convertRaw($other['raw_type'],"#1cff03","__RAW_VALUE_TYPE__",$body);
        if(isset($other['third_opt']))
            $body = $this->convertRaw(htmlspecialchars(implode(", ",$other['third_opt'])),"#65fad8","__THIRD_OPT__",$body);

        $stack = "<div style='padding-left: 10px'>";
        $stack_raw = "";
        foreach ($other['stack'] as $k => $v){
            $k++;
            $stack .= <<<STACK
<b style="color: magenta">Level $k</b>
<div style="color: #fff; padding-left: 20px">
    <div><i style="color: greenyellow">file:</i> {$v['file']}</div>
    <div><i style="color: greenyellow">line:</i> {$v['line']}</div>
    <div><i style="color: greenyellow">function:</i> {$v['function']}</div>
</div>
STACK;
            $stack_raw .= <<<STACK
-- Level $k
-------- file: {$v['file']}
-------- line: {$v['line']}
-------- function: {$v['function']}

STACK;
        }
        $stack .="</div>";

        if(self::$ENV == "DEVELOPMENT" || $other['core'] == "view") {
            echo <<<DEBUG
<div style='background:#1d2124;padding:5px;border-radius:5px;color:#fffffa;overflow:auto;'>
    <h3 style='text-transform: uppercase; color: $title_color'>$title</h3>
    <div style='color: $body_color; font-weight: bold; margin: 5px 0;'>$body</div>
    <div><h4 style="margin-bottom: 2px; color: #0099ff; text-transform: uppercase">FOOT NOTE</h4><b>Stack:</b> $stack</div>
    <div><b>Env:</b> <b style="color: #dea303">$env</b></div>
</div>
DEBUG;
            return $other['act'] ?? "kill";
        }
        else{
            $dir = explode("osai",__DIR__)[0] . "osai/";
            $file = $dir . "SQL_LOG";
            $file_log = $file . "/log.txt";
            if(!is_dir($file)) mkdir($file,0755);
            if(!file_exists($file_log)) $fh = fopen($file_log,"w+");
            else {
                $fh = fopen($file_log,"r+");
                fseek($fh,0,SEEK_END);
            }
            $body = strip_tags($body);
            $date = date("Y-m-d H:i:s");
            fwrite($fh,<<<DEBUG
**** $title ****
########################
$body
########################
#### Time: $date
#### Stack:
$stack_raw---------------------------END###

DEBUG) or die("Unable to write SQL error log in location " . $file . " <br> Refer to " . __FILE__ . ": " . __LINE__);
            fclose($fh);
            echo "You have SQL error, check your sql error log for details";
            return $other['act'] ?? "allow";
        }
    }
    private function convertRaw($print_val,$span_colour,$replace,$body) : string {
        ob_start();
        print_r($print_val);
        echo "<div>" . gettype($print_val) . "</div>";
        $x = ob_get_clean();
        $x = empty($x) ? "**NO VALUE PASSED**" : $x;
        $x = "<span style='margin: 10px 0 1px; color: $span_colour'>$x</span>";
        return str_replace($replace, $x, $body);
    }
    protected function show_exception($type, $opt=[]) : void {
        $query=$opt[0] ?? ""; $query_type=$opt[1] ?? "";
        $dbg = debug_backtrace(2);
        switch ($type){
            #### SQR = Structured Query Review  &&&   SQE = Structured Query Error
            case -1:
                $act = $this->container("Query Review [SQR::-1]","<pre style='color: #dea303 !important'>$query</pre>",["stack"=>$dbg,"core"=>"view"]);
                break;
            case 1:
                $act = $this->container("Cleanse Error [SQE-CL::1]","No value passed!<br> <em>An empty string cannot be cleaned</em>",["stack"=>$dbg,"core"=>"error"]);
                break;
            case 1.5:
                $act = $this->container("Cleanse Error [SQE-CL::1.5]","No Valid Cleanse Combo or Level passed!<br> 
            Expected: <em>[0-5]</em> or unique combination of the range in <em>COMBO MODE</em> OR <em>[10-20]</em> in <em>LEVEL MODE</em><br> Got: <b>Level/Combo:</b> __RAW_VALUE_TYPE__",["stack"=>$dbg,"core"=>"error","raw_type"=>$query_type]);
                break;
            case 2:
                $act = $this->container("Cleanse Error [SQE-CL::2]","A Non-String Value was encountered! __RAW_VALUE__",["stack"=>$dbg,"core"=>"error","raw_value"=>$query]);
                break;
            case 3:
                $act = $this->container("Cleanse Debug [SQR-CL::3]","<b>Value:</b> __RAW_VALUE__<br> <b>Level/Combo:</b> __RAW_VALUE_TYPE__<br> <b>Extra Param:</b> __THIRD_OPT__",["stack"=>$dbg,"core"=>"view", "raw_value"=>$query, "raw_type"=>$query_type,"third_opt"=>$opt[2]]);
                break;
            case 4:
                $act = $this->container("Connection Test [SQR-CO::4]","No connection detected: <h5 style='color: #008dc5'>Connection might be closed:</h5>",["stack"=>$dbg,"core"=>"error"]);
                break;
            case 5:
                $db = $opt[0]; $usr = $opt[1]; $host = $opt[2];
                $act = $this->container("Connection Test [SQR-CO::5]", "<h2>Connection Established!</h2><u>Your connection info states:</u><div style='color: gold; font-weight: bold; margin: 5px 1px;'>&gt; Host: <u>".$host."</u></div><div style='color: gold; font-weight: bold; margin: 5px 1px;'>&gt; User: <u>".$usr."</u></div><div style='color: gold; font-weight: bold; margin: 5px 1px;'>&gt; Database: <u>".$db."</u></div>", ["stack"=>$dbg,"core"=>"success"]);
                break;
            case 6:
                $act = $this->container("Connection Error [SQE-CO::6]","<div style='color: #e00; font-weight: bold; margin: 5px 1px;'>".mysqli_connect_error()."</div>",["stack"=>$dbg,"core"=>"error","act"=>"kill"]);
                break;
            case 7:
                $act = $this->container("Connection Error [SQE-CO::7]","<div style='color: #e00; font-weight: bold; margin: 5px 1px;'>Failed to close connection. No pre-existing mySQL connection</div>",["stack"=>$dbg,"core"=>"error","act" => "kill"]);
                break;
            case 9:$act = $this->container($opt['title'],$opt['body'],["stack"=>$dbg,"core"=>"error","act" => @$opt['kill'] ? "kill" : "allow"]);break;
            default:
                $act = $this->container("Query Execution Error [SQE::0]","<b style='color: #008dc5'>".mysqli_error($this->get_link())."</b> <div style='margin: 10px 0'>__RAW_VALUE_TYPE__ Query</div>", ["stack"=>$dbg,"core"=>"error","raw_type"=>$query_type,"act"=>"kill"]);
                break;
        } if($act == "kill") exit();
    }
}
