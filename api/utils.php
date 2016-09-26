<?php
function is_assoc($array) {
    if(is_array($array)) {
        $keys = array_keys($array);
        return $keys != array_keys($keys);
    }
    return false;
}
?>
