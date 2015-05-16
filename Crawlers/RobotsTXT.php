<?php
namespace Crawlers;

use Utils\Writer;

class RobotsTXT extends CURLRequest {
    
    public function __construct() {}
    
    public function process($url) {
        $robotURL = $this->getRobotURL($url);
        if(file_exists('cache/robots/'.md5($robotURL).'.txt')) {
            echo "\nRead robots.txt from cache...\n";
            $robotURL = 'cache/robots/'.md5($robotURL).'.txt';
            $content = file_get_contents($robotURL);
        } else {
             $content = $this->fetchURI($robotURL)->response;
             $this->store($content, $url);
        }
        
        // TODO: fire this through the same HEAD logic as a page fetch
        // if we do this perhaps we need a new util class to hold this functionality, or a base class both Crawler and RobotsTXT can extend?
        // we want the head request to get the file m time, to see if we should grab it again (though we'll probbaly only do this head request every so often and
        // assume the local cache is valid for a while)
        
       
        $rules = $this->parse($content);
        $urlData = parse_url($url);
        $ok = true;
        
        foreach($rules as $rule) {
            if(strpos($url, $rule) !== false) {
                echo "\nUnable to process URL $url, due to rule $rule ... \n";
                $ok = false;
                break;
            }
        }
        // echo $content;
        
        
        
        return $ok;
    }
    
    private function getRobotURL($url) {
        $urlParts = parse_url($url);
        return $urlParts['scheme'] . '://' . $urlParts['host'] . '/robots.txt';
    }
    
    public function parse($content) {
        // check only for user agent: * for now (all agents)
        // and only for disallow rules
        // (User-agent:\s*\*|Disallow:\s*([\/a-z0-9]+))
        $currentAgent = '';
        $content = explode("\n", $content);
        foreach($content as $rule) {
            // ignore commented lines
            if(substr($rule, 0, 1) == '#') {
                continue;    
            }
            
            if(preg_match('/User-agent:\s*(.+)/i', $rule, $matches)) {
                if(trim($matches[1]) == '*') {
                    echo "\nfound rule for all agents ... \n";
                    $currentAgent = '*';
                } else {
                    $currentAgent = $matches[1];
                }
            }
            
            if($currentAgent == '*' && preg_match('/Disallow:\s*(.+)/i', $rule, $matches)) {
                // echo "\nDisallow rule found for agent $currentAgent ... \n";
                $rules[urldecode($matches[1])] = urldecode($matches[1]);
            }
        }
        
        // echo "\n RULES: \n";
        // print_r($rules);
        
        return $rules;
    }
    
    private function store($content, $url) {
        $robotURL = $this->getRobotURL($url);
        $content = time() . "\n$url\n$robotURL\n$content";
        // file_put_contents('cache/robots/' . md5($robotURL) . '.txt', $content);
        Writer::save('cache/robots/' . md5($robotURL) . '.txt', $content);
    }
    
}