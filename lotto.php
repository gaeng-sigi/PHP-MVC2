<?php

$arr = array();

for ( $i=0; $i<6; $i++) {
  $arr[$i] = rand(1, 45);
  for($j=0; $j<$i; $j++)
  {
    if($arr[$i] == $arr[$j]) {
      $i--;
      break;
    }
  }
}

print_r($arr);