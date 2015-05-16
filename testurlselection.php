<?php

$urls = array(
    1 => array(
        'http://www.bbc.co.uk',    
        'http://en.wikipedia.org',
        'http://www.bittech.net',
    ),
    2 => array(
        'http://www.bbc.co.uk/news',    
        'http://en.wikipedia.org/wiki',
        'http://www.bittech.net/hardware',
    ),
    3 => array(
        'http://www.bbc.co.uk/news/uk',    
        'http://en.wikipedia.org/wiki/Albert_einstein',
        'http://www.bittech.net/hardware/cpus',
    ),
);

$weights = array(1 => 0.5, 2 => 0.3, 3 => 0.2);
$res = array();
$length = 10;
$count = 0;

// for($i=0; $i<$length; $i++) {
while($count < $length) {
    echo count($res, COUNT_RECURSIVE);
    echo " $length \n";
    
    foreach($weights as $depth => $probability) {
        $test = mt_rand(1, $length);
        if($test<=$probability*$length) {
            $res[$depth][] = $urls[$depth][array_rand($urls[$depth])];
            $count++;
        }  
    }
}

ksort($res);
print_r($res);