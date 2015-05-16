<?php
/**
 * Ranking:
 * document score - BM25F
 * link score - incoming links (outgoing as well?)
 * path term score - term score for pages across the same path (or higher up it?)
 * path score - term score for path itself (i.e. www.domain.com/path/path2/doc.html)
 * language score?
 * readability score?
 */ 

require 'bootstrap.php';

use Tokenizers\EnglishTokenizer;
use Utils\Timer;

function rawTF($tf) {
  return $tf;
}

function logTF($tf) {
  return 1+log($tf);
}

function augTF($tf, $maxTF) {
  return 0.5 + ((0.5*$tf)/$maxTF);
}


function bm25Score($tf, $idf, $docLen, $avgDocLen) {
  $k1 = 1.2;
  $b = 0.75;

  return $idf * (($tf * ($k1 + 1)) / (($tf + $k1) * ((1-$b) + ($b*($docLen / $avgDocLen)))));
}

$invertedIndex = file_get_contents('data/invertedindex.dat');
$invertedIndex = unserialize($invertedIndex);

$domainIndex = file_get_contents('data/domainindex.dat');
$domainIndex = unserialize($domainIndex);

$docMetaIndex = file_get_contents('data/docmetaindex.dat');
$docMetaIndex = unserialize($docMetaIndex);

// $query = array('bbc');
array_shift($argv);
$query = $argv;

echo "\nQUERY: ";
print_r($query);


Timer::start('Searching');
$totalMatches = 0;
$avgDocLen = floor($docMetaIndex['totalLen'] / $docMetaIndex['numDocs']);

$termsMatched = 1;
foreach($query as $qIndex => $term) {
  $termData = $invertedIndex[$term];
  $idf = log($docMetaIndex['numDocs'] / (1 + count($termData)));
  
  foreach($termData as $doc) {
    $domainData = parse_url($doc['url']);
    $domain = $domainData['host'];
    
    if($termPosTitle = stripos($doc['title'], $term)) {
      $titleBoost = (0.5+((0.5*$termPosTitle)/strlen($doc['title']))) * $idf;
      // echo "\nTitle boost for {$doc['title']} = $titleBoost...\n";
    }
    
    $scores[$doc['url']]['title'] = $doc['title'];
    // echo "\n$domain: {$domainIndex[$domain][$term]}\n";
    // $scores[$doc['url']]['score'] += (rawTF($doc['tf']) * $idf) + (1/strlen($doc['url'])) + $domainIndex[$domain][$term];
    $scores[$doc['url']]['score'] += bm25Score($doc['tf'], $idf, $doc['docLen'], $avgDocLen) + ($titleBoost * 10);
    // $scores[$doc['url']]['score'] += ($doc['tf'] * $idf);
    $scores[$doc['url']]['score'] *= $termsMatched;
  }
  
  $termsMatched++;
}

uasort($scores, function($a, $b) {
  return $a['score'] <= $b['score'];
});

// arsort($scores);
$totalMatches = count($scores);

echo "\n$totalMatches results ...\n";

Timer::end('Searching');

$results = array_slice($scores, 0, 10, true);
// $results = array_reverse($results, true);
foreach($results as $url => $data) {
    echo "\n{$data['title']}\n$url\n{$data['score']}...";
}

print_r(Timer::getTimes());
