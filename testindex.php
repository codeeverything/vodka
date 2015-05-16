<?php
require 'bootstrap.php';


use Indexers\HTMLIndexer;
use Tokenizers\EnglishTokenizer;
use Utils\Timer;
use Utils\BinaryBloomFilter;



$librarian = new HTMLIndexer(new EnglishTokenizer());
Timer::start('Indexing');
$zip = new ZipArchive;
Timer::start('Decompress');

$testCount = 0;
$dir = new DirectoryIterator('cache/documents');
foreach ($dir as $fileinfo) {
    if($fileinfo->isDir()) continue;
    $filename = $fileinfo->getPathname();
    if ($zip->open($filename) === TRUE) {
        $data = $zip->getFromName('page.html');
        $separator = "\n";
        $crawlTime = strtok($data, $separator ) .  "\n";
        $docURL = strtok( $separator ) .  "\n";
        // echo $data;
        $zip->close();
    } else {
        echo 'failed';
    }
    $librarian->index($data);
    
    // usleep(rand(100, 300)*1000);
    // sleep(1);
    
    if($testCount > 500) break;
    $testCount++;
    echo "\nMem usage: " . (memory_get_usage() / 1024 / 1024) . "MB\n";
    echo "Completed indexing $testCount docs ...\n";
}

Timer::end('Indexing');

print_r(array_slice($librarian->domainIndex['www.bbc.co.uk'], 0, 10, true));

ksort($librarian->invertedIndex);
// print_r($librarian->invertedIndex);
echo count($librarian->invertedIndex);

$serialInverted = serialize($librarian->invertedIndex);
file_put_contents('data/invertedindex.dat', $serialInverted);
file_put_contents('data/domainindex.dat', serialize($librarian->domainIndex));
file_put_contents('data/docmetaindex.dat', serialize($librarian->docMetaIndex));

print_r(Timer::getTimes());