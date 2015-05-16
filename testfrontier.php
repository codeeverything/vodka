<?php
require 'bootstrap.php';

use Utils\Timer;

$frontier = file_get_contents('data/frontier.txt');
$frontier = unserialize($frontier);

echo count($frontier);

Timer::start('ChunkFrontier');
$depthChunkIndex = unserialize(file_get_contents('data/frontier/depthChunkIndex.txt'));
// print_r($depthChunkIndex);

foreach($frontier as $depth => $urls) {
    if(!file_exists('data/frontier/'.$depth)) {
        echo "\nCreate $depth ...";
        mkdir('data/frontier/'.$depth);
    } 

    if(!array_key_exists($depth, $depthChunkIndex)) {
        $currentChunkCount = 0;
    } else {
        $currentChunkCount = $depthChunkIndex[$depth];
    }
    
    // $currentChunkCount = iterator_count(new DirectoryIterator('data/frontier/'.$depth)) - 2;
    echo "\nDepth $depth has $currentChunkCount chunks at present ...";

    
    $chunkSize = 1000;
    $urlChunks = array_chunk($urls, $chunkSize);
    $chunkCount = count($urlChunks);
    $depthChunkIndex[$depth] = $currentChunkCount + $chunkCount;
    
    echo "\nDepth $depth split into $chunkCount chunks of $chunkSize URLS ...";
    
    foreach($urlChunks as $urlChunk) {
        $currentChunkCount++;
        echo "\nDepth $depth, 'chunk_$currentChunkCount.txt' ...";
        file_put_contents('data/frontier/'.$depth.'/'.'chunk_'.$currentChunkCount.'.txt', serialize($urlChunk));
    }
}

file_put_contents('data/frontier/depthChunkIndex.txt', serialize($depthChunkIndex));

Timer::end('ChunkFrontier');


// now rebuild the frontier
// TODO: Need some way of tracking the smallest chunk per depth and max
// we'll need the smallest when building the frontier as that'll be the oldest
// and we'll need the max so we know what index to give each chunk file when 
// persisting the frontier

Timer::start('BuildFrontier');
$frontier = array();
$dir = new DirectoryIterator('data/frontier/');
foreach($dir as $d) {
    if(!$d->isDot()) {
        var_dump($d->getPathName());
        $file = file_get_contents($d->getPathName() . '/chunk_1.txt');
        $file = unserialize($file);
        $depth = array_pop(explode('/', $d->getPathName()));
        $frontier[$depth] = array_splice($file, 0, 100);
        // now write back the file after we've pulled data from it
    }
}

Timer::end('BuildFrontier');

// krsort($frontier);
// print_r($frontier);

Timer::getTimes();