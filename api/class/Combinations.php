<?php 
function uniqueCombination($in, $minLength = 1, $max = 2000) {
    $count = count($in);
    $members = pow(2, $count);
    $return = array();
    for($i = $members - 1; $i > 0 ; $i--) {
        $b = sprintf("%0" . $count . "b", $i);
        $out = array();
        for($j = 0; $j < $count; $j ++) {
            $b{$j} == '1' and $out[] = $in[$j];
        }

        count($out) >= $minLength && count($out) <= $max and $return[] = $out;
        }
    return $return;
}
?>