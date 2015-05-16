<?php

ini_set('display_errors', true);
error_reporting(E_ALL);
$sock = socket_create_listen(1234); 
socket_getsockname($sock, $addr, $port); 
print "Server Listening on $addr:$port\n"; 
while($c = socket_accept($sock)) { 
  /* do something useful */ 
  socket_getpeername($c, $raddr, $rport); 
  print "Received Connection from $raddr:$rport\n"; 
} 
socket_close($sock); 