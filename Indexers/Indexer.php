<?php

namespace Indexers;
use Interfaces\IIndexer;
//https://github.com/webmil/text-language-detect
use TextLanguageDetect\TextLanguageDetect;
use TextLanguageDetect\LanguageDetect\TextLanguageDetectException;

abstract class Indexer implements IIndexer {
    protected $tokenizer;
    protected $langDetect;
    
    public function __construct($tokenizer) {
        echo "indexing";
        $this->tokenizer = $tokenizer;
        $this->langDetect = new TextLanguageDetect();
    }
}