<?php

$frontier = file_get_contents('data/frontier.txt');
$frontier = unserialize($frontier);
echo "<pre>";
print_r($frontier);
echo "</pre>";