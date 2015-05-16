<?php

namespace Indexers;

use Utils\Timer;

class HTMLIndexer extends Indexer {
    public $invertedIndex = array();
    public $domainIndex = array();
    public $docMetaIndex = array();
    
    public function index($data) {
        $dom = new \DOMDocument;
        $dom->loadHTML($data);
        
        //get title
        $titleString = $this->getTitle($dom);
        $title = $this->tokenizer->tokenize($titleString);
        
        //get micro data
        // $microData = $this->getOpengraphData($dom);
        
        Timer::start('HTMLIndexer::getText');
        $text = $this->getText($dom);
        Timer::end('HTMLIndexer::getText');
        
        $this->docMetaIndex['numDocs']++;
        $this->docMetaIndex['totalLen'] += strlen($text);
        
        try {
            //return 2-letter language codes only
            $this->langDetect->setNameMode(2);
        
            $result = $this->langDetect->detect($text, 4);
            // print_r($result);
            $lang = key($result);
            if($lang != 'en') return;
        } catch (TextLanguageDetectException $e) {
            die($e->getMessage());
        }
        
        $tokens = $this->tokenizer->tokenize($text);
        
        $separator = "\n";
        $crawlTime = strtok($data, $separator ) .  "\n";
        $docURL = strtok( $separator ) .  "\n";
        $domainData = parse_url($docURL);
        $domain = $domainData['host'];
        
        foreach($tokens as $token => $tCount) {
            //how popular is that term on that domain?
            $this->domainIndex[$domain][$token]++;
            
            $this->invertedIndex[$token][] = array('tf' => $tCount, 'url' => $docURL, 'title' => $titleString, 'docLen' => strlen($text));
            usort($this->invertedIndex[$token], function($a, $b) {
                return $a['tf'] <= $b['tf'];
            });
        }
        
        uasort($this->domainIndex[$domain], function($a, $b) {
            return $a <= $b;
        });
    }
    
    private function getText($dom) {
        $xpath = new \DOMXPath($dom);
        $textnodes = $xpath->query('//text()[normalize-space() and not(ancestor::script | ancestor::style)]');
        $text = '';
        $numTextNodes = $textnodes->length;
        for ($i = 0; $i < $numTextNodes; $i++) {
            $node = $textnodes->item($i);
            $textContent = ' '  . $node->textContent;
            // $textContent = str_replace(array("\r", "\n", "\t"), ' ', $textContent);
            $textContent = preg_replace('/[^\w\d]+/m', ' ', $textContent);
            $text .= $textContent;
        }
        
        return strtolower($text);
    }
    
    private function getOpengraphData($dom) {
        $xpath = new \DOMXPath($dom);
        $query = '//*/meta[starts-with(@property, \'og:\')]';
        $metas = $xpath->query($query);
        foreach ($metas as $meta) {
            $property = $meta->getAttribute('property');
            $content = $meta->getAttribute('content');
            $rmetas[$property] = $content;
        }
        var_dump($rmetas);
    }
    
    private function getHTML5Microdata($dom) {
        $xpath = new \DOMXPath($dom);
        $query = '//*[@itemprop]';
        $nodes = $xpath->query($query);
        if($nodes->length > 0) {
            var_dump($nodes);
            for ($i = 0; $i < $nodes->length; $i++) {
                var_dump($nodes->item($i));
            }
        }
        return $nodes->length > 0 ? true:false;
    }
    
    private function getTitle($dom) {
        $xpath = new \DOMXPath($dom);
        $query = '//title';
        $title = $xpath->query($query);
        $title = $title->item(0)->textContent;
        var_dump($title);
        return $title;
    }
    
    
}