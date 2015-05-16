<?php
namespace Interfaces;
use Crawlers\CrawlManager;

interface ICrawler {
    public function __construct(CrawlManager &$crawlManager);
    public function fetch($url);
}