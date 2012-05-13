<?php
      echo (string) microtime().'<br>';
    $temp_ary = explode(' ', (string) microtime());
    echo substr($temp_ary[0],2).'<br>';
    echo date('YmdHis') . "." . substr($temp_ary[0],2);
    exit;
?>
