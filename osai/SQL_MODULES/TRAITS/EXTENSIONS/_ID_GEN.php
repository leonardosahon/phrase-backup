<?php
declare(strict_types=1);
namespace osai\SQL_MODULES\TRAITS\EXTENSIONS;
use osai\SQL;

trait _ID_GEN {
    /**
     * @param array $option
     * "seed" => {string|null},
     * "attach" => {string|null} ["pre"],
     * "attach_opt" => {array|null} [("pre" || 0) => {string}, ("post" || 1) => {string}],
     * "digit" => {int},
     * "table" => {string|null},
     * "col" => {string|null}
     * @return string|null
     */
    public function gen_id(array $option = []) : ?string{
        $seed = $option['seed'] ?? null;
        $attach = $option['attach'] ?? "pre";
        $attach_opt = $option['attach_opt'] ?? null;
        $digit_length = ($option['digit'] ?? 7) - 1;
        $confirm_table = $option['table'] ?? null;
        $confirm_column = $option['col'] ?? null;

        $min = 10 ** $digit_length;
        $max = 9 * $min;

        if($attach == "pre" || $attach == "prepend")
            $rand = $seed.rand($min,$max);
        elseif($attach == "post" || $attach == "append")
            $rand = rand($min,$max).$seed;
        elseif ($attach_opt) {
            $rand = ($attach_opt['pre'] ?? $attach_opt[0]) . rand($min, $max) . ($attach_opt['post'] ?? $attach_opt[1]);
        }
        else
            $rand = rand($min,$max)."";

        if($confirm_table && $confirm_column) {
            if (self::core()->count("id", $confirm_table, "WHERE $confirm_column='$rand'") > 0)
                return $this->gen_id([
                    "seed" => $seed,
                    "attach" => $attach,
                    "attach_opt" => $attach_opt,
                    "digit" => $digit_length,
                    "table" => $confirm_table,
                    "col" => $confirm_column
                ]);
        }
        return $rand;
    }
}
