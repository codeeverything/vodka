<?php
require 'bootstrap.php';

use Crawlers\CrawlManager;
use Crawlers\Crawler;
use Indexers\HTMLIndexer;
use Tokenizers\EnglishTokenizer;
use Utils\Timer;
// use Utils\BloomFilter;
use Utils\BinaryBloomFilter;


$crawlManager = new CrawlManager(new BinaryBloomFilter(250000000, 5), new BinaryBloomFilter(25000000, 5));
$crawlManager->run(array(array('href' => "http://en.wikipedia.org/wiki/Bloom_filter"), array('href' => "http://www.bbc.co.uk/news"), array('href' => "http://www.bittech.net")));
// $crawlManager->run(array(array('href' => "http://en.wikipedia.org/wiki/Bloom_filter")));

$speedy = new Crawler($crawlManager);
$speedy->run();

// Timer::start('Indexing');
// $librarian = new HTMLIndexer(new EnglishTokenizer());
// $librarian->index($data);
// Timer::end('Indexing');

Timer::getTimes();

echo "ok";
