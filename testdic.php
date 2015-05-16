<?php
require 'bootstrap.php';

use Utils\Timer;

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function fast_in_array($elem, $array) 
{
   $top = sizeof($array) -1;
   $bot = 0;
   while($top >= $bot) 
   {
      $p = floor(($top + $bot) / 2);
      if ($array[$p] < $elem) $bot = $p + 1;
      elseif ($array[$p] > $elem) $top = $p - 1;
      else return TRUE;
   }
   return FALSE;
}

echo "Loading dictionary ...\n";
$dic = array();
for($i=0;$i<600000; $i++) {
    $dic[] = generateRandomString();
    // $dic[] = 't';
}
sort($dic);
echo "Loaded ... \n";
echo (memory_get_usage()/1024/1024);

echo "\nWaiting ... \n";
sleep(1);

echo "Doing ... \n";
for($i=0;$i<1000000;$i++) {
    // Timer::start('Binary');
    $foo = fast_in_array('test', $dic);
    // Timer::end('Binary');
    if($i % 10000 == 0) {
        $load = sys_getloadavg();
	    echo $load[0] . "%\n";
    }
    usleep(100);
}

print_r(Timer::getTimes());

die();

$handle = fopen ("php://stdin","r");

while($line = fgets($handle)) {
    $line = trim($line);
    if(trim($line) == 'exit'){
        echo "ABORTING!\n";
        exit;
    }
    
    if($line) {
        // Timer::start('Lookup');
        // $foo = in_array($line, $dic);
        // Timer::end('Lookup');
        
        Timer::start('Binary');
        $foo = fast_in_array($line, $dic);
        Timer::end('Binary');
        
        var_dump($foo);
        $randIndex = array_rand($dic);
        echo "$randIndex - " . $dic[$randIndex] . "\n";
        print_r(Timer::getTimes());
    }
}
