<?php

namespace Crawlers;
use Crawlers\Crawler;

class CrawlManager {
    private $crawlers = array();
    private $urlMasterList = array(1=>array(), 2=>array(), 3=>array());
    private $bloom;
    private $hostBloom;
    private $currentFrontierIndex = 0;
    
    public function __construct($bloom, $hostBloom) {
        $this->bloom = $bloom;
        $this->hostBloom = $hostBloom;
        
        if(file_exists('data/bloom.txt')) {
			$this->bloom = unserialize(file_get_contents('data/bloom.txt'));
		}
		
		if(file_exists('data/host_bloom.txt')) {
			$this->hostBloom = unserialize(file_get_contents('data/host_bloom.txt'));
		}
        
        //load the url frontier
        $frontier = file_get_contents('data/frontier.txt');
        if($frontier) $this->urlMasterList = unserialize($frontier);
        // var_dump($this->urlMasterList);
        
        $this->frontierSize = count($this->urlMasterList, COUNT_RECURSIVE);
    }
    
    public function __destruct() {
        file_put_contents('data/frontier.txt', serialize($this->urlMasterList));
        $this->urlMasterList = array();
        echo "\nMemory after persisting frontier: ". (memory_get_usage(true) / 1024 /1024) . 'MB';
        file_put_contents('data/bloom.txt', serialize($this->bloom));
        file_put_contents('data/host_bloom.txt', serialize($this->hostBloom));
    }
    
    public function run($seedURLs) {
        if(is_array($seedURLs)) {
            $this->urlMasterList[1] = $this->urlMasterList[1] + $seedURLs;
        } else {
            $this->urlMasterList[1] = $this->urlMasterList[1] + array('href' => $seedURLs);
        }
        
        
        // var_dump($this->urlMasterList);
    }
    
    public function registerCrawler() {
        $id = $this->setCrawlerID();
        $this->crawlers[$id] = array(); //details...
        
        return $id; //send the ID back to the crawler
    }
    
    public function setCrawlerID() {
        return \uniqid('');
    }
    
    public function processNewURLS($urls) {
        $newCount = 0;
        foreach($urls as $idx => $url) {
            
            $urlData = parse_url($url['href']);
            if(!$this->hostBloom->maybeInSet($urlData['host'])) {
                // echo "\nAdded host {$urlData['host']} to HostBloom ... \n";
                $this->hostBloom->add($urlData['host']);
            } else {
                // echo "\nHost {$urlData['host']} found in HostBloom ... \n";
            }
            
            if(strpos($url['href'], '?') === false && !$this->bloom->maybeInSet($url['href'])) {
                $this->urlMasterList[$url['depth']][] = $url;   
                $this->bloom->add($url['href']);
                $newCount++;
            } else {
                // echo "\nURL ".$url['href']." already in frontier...\n";
            }
        }
        
        
        //TODO: Occassionally we'll need to persist the frontier to disk to keep memory from going bonkers
        // perhaps push into a structure like data/frontier/{depth}/{chunk X}.txt (where lower number indicate older chunks)
        // where a chunk is some number of URLs long, Y
        // then when reading from the frontier build it with some proportion of URLs from each of the depth's most recent chunks
        // until we have a frontier big enough for memory
        
        echo "\nAdded $newCount new URLS to frontier...\n";
        echo "\nFrontier now ".count($this->urlMasterList, COUNT_RECURSIVE)." URLs long...\n";
    }
    
    private function normalizeLink($link) {
        $link = rtrim($link, '/');
        $link = rtrim($link, '/');
        $link = rtrim($link, '/');
        $link = urldecode($link);
        
        //remove any hashbang stuff
        if($pos = strpos($link, '#')) {
            $link = substr($link, 0, $pos);
        }
        
        return $link;
    }
    
    public function getTestURLSet() {
        $test = $this->urlMasterList[1];
        usort($test, function($a, $b) {
            return strlen($a['href']) >= strlen($b['href']) ? 1:-1;
        });
        
        // print_r(array_slice($test, 0, 100));
        // die();
        // shuffle($test);
        return array_slice($test, 0, 100);
    }
    
    public function getURLSet() {
        echo "\ngetting url set...\n";
        // var_dump(array_splice($this->urlMasterList[1], 0, 3));
        $depthOne = array_splice($this->urlMasterList[1], 0, 100);
        $depthTwo = array_splice($this->urlMasterList[2], 0, 60);
        $depthThree = array_splice($this->urlMasterList[3], 0, 40);
        // var_dump($depthOne, $depthTwo, $depthThree);
        
        
        // sleep(2);
        $urls = array_merge($depthOne, $depthTwo, $depthThree);
        // var_dump($urls);
        
        usort($urls, function($a, $b) {
            return strlen($a['href']) >= strlen($b['href']) ? 1:-1;
        });
        
        return $urls;
        //consider these urls processed
        //TODO this is bad as the crawler could fail on them, so in future capture the status
        //after the crawler has run
        //store in the "oldcountry" url list. when checking to see if a URL should be crawled check both the
        //frontier and the oldcountry?
        
        // TODO: we should prioritise:
        // links with a lower depth
        // links with shorter paths (more readable)
        // links which point to novell hosts 
        // links to hosts we have fewer crawled pages for - i.e. if we have links to example.com and foo.com 
        // and example.com has 100 crawled URLs vs foo.com's 10, we prefer the link to foo.com.
        // the idea hear is not to get bogged down crawling the whole of example.com, but to be broader in the crawl
        // (might implement a counting bloom filter here and increment for every X pages rather than every 1)
    }
    
}