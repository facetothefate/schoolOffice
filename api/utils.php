<?php
function is_assoc($array) {
    if(is_array($array)) {
        $keys = array_keys($array);
        return $keys != array_keys($keys);
    }
    return false;
}
function generatePassword($length=8)
{
    $chars = array_merge(range(0,9),
                     range('a','z'),
                     range('A','Z'),
                     array('!','@','$','%','^','&','*'));
    shuffle($chars);
    $password = '';
    for($i=0; $i<8; $i++) {
        $password .= $chars[$i];
    }
    return $password;
}
?>
