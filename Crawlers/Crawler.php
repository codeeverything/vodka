<?php
namespace Crawlers;
use Crawlers\CURLRequest;
use Interfaces\ICrawler;
use Crawlers\RobotsTXT;
use Utils\Timer;
use Utils\ZIPWriter;

class Crawler extends CURLRequest implements ICrawler {
    private $crawlerID;
    private $urlList;
    private $crawlManager;
    private $_safeHTTPCodes = array(200, 301, 302);
    const RECURSION_TOLERANCE = 4;
    
    public function __construct(CrawlManager &$crawlManager) {
        echo "crawl!";
        
        if(!$crawlManager) {
            throw new Exception("Crawl Manager Missing");    
        }
        
        $this->crawlerID = $crawlManager->registerCrawler();
        var_dump($this->crawlerID);
        
        $this->crawlManager = $crawlManager;
        $this->urlList = $this->crawlManager->getURLSet();
        // $this->urlList = $this->crawlManager->getTestURLSet();
        // var_dump($this->urlList);
    }    
    
    public function run() {
        // Timer::start('MultiTest');
        // //init out curl multi object
        // $mc = curl_multi_init();
        
        // $urls = array_splice($this->urlList, 0, 200);
        
        // //push each URL onto the curl multi handler
        // foreach ($urls as $i => $url) {
        //     // $conn = curl_init($url);
        //     $conn = curl_init($url['href']);
        //     curl_setopt($conn, CURLOPT_USERAGENT, 'VodkaBot .dev (https://github.com/inkymike81/vodka)');
        //     curl_setopt($conn, CURLOPT_RETURNTRANSFER, 1);
        //     curl_setopt($conn, CURLOPT_ENCODING , "gzip");
        //     curl_setopt($conn, CURLOPT_TIMEOUT, 10);
        //     // curl_setopt($conn, CURLOPT_CONNECTTIMEOUT, 2);
        //     // curl_setopt($conn, CURLOPT_NOBODY, 1);
        //     curl_setopt($conn, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        //     curl_setopt($conn, CURLOPT_SSL_VERIFYPEER, FALSE);
        //     curl_setopt($conn, CURLOPT_SSL_VERIFYHOST, FALSE);
        //     // curl_setopt($conn, CURLOPT_VERBOSE, 1);
        //     if(!$this->checkRobotsTXT($url)) {
        //         echo "\nURL excluded from MC by robots.txt rule... \n";
        //         continue;
        //     }
            
        //     curl_multi_add_handle($mc, $conn);
        //     echo "\nAdding {$url['href']} to the crawl ...\n";
        // }
    
        
        
        // $bittech = false;
        // $urlsCrawled = 0;
        
        // echo "\nBeginning crawl...\n";
        
        // //keep looping while the curl multi handler has something to process
        // //probably just want to loop forever until some error condition is met, or
        // //until some period has passed/number of urls explored, then kill ourselves
        // //i.e. keep going even if there's nothing to do (which should never happen)
        // do {
        //     //TODO: Need to handle HEAD request, redirects and none HTML pages in here
        //     //execute the stack of urls
        //     $status = curl_multi_exec($mc, $active);
        //     //get the status of the requests
        //     $info = curl_multi_info_read($mc, $msgsLeft);
            
        //     //if we have some response from some request
        //     if (false !== $info) {
        //         // echo "\nRead info, $msgsLeft messages left in queue...\n";
        //         // var_dump($info);
        //         $curlInfo = curl_getinfo($info['handle']);
        //         // $responses[$curlInfo['url']] = $curlInfo['http_code'] . ' -  ' . $curlInfo['total_time'];
        //         // $responses[$curlInfo['url']] = $curlInfo;
        //         $responses[$curlInfo['http_code']]++;
                
        //         $urlData = parse_url($curlInfo['url']);
        //         $baseURL = $urlData['scheme'] . '://' . $urlData['host'] . '/';
                
        //         if($curlInfo['http_code'] == 200) {
        //             $content = curl_multi_getcontent($info['handle']);
        //             // echo $content;
        //             $links = $this->getLinks($content, $baseURL);
                    
        //             // $this->store($content, $url);
                    
        //             $fetched = array(
        //                 'links' => $links,
        //             );
                    
        //             $this->crawlManager->processNewURLS($fetched['links']);
        //         }
                
        //         echo "\nFinished crawling {$curlInfo['url']} with HTTP code {$curlInfo['http_code']} ... \n";
                
        //         //update the hostHitList for this host with the current time
        //         // $urlData = parse_url($curlInfo['url']);
        //         $host = $urlData['host'];
        //         $hostHitList[$host] = time();
                
        //         //asssign this handle a new url and push it back onto the stack
        //         //for the curl multi handler
        //         $handle = $info['handle'];
        //         //remove the existing handle from the multi stack to avoid overloading this with idle handlers
        //         curl_multi_remove_handle($mc, $handle);
        //         //this url will come from an array_shift() on $this->urlList
        //         $nextURL = array_shift($this->urlList); 
        //         $urlData = parse_url($nextURL['href']);
        //         $host = $urlData['host'];
        //         // if(array_key_exists($host, $hostHitList)) {
        //         //     echo "\nBailing on url {$nextURL['href']} as we already hit that host ... \n";
        //         // }
        //         // if(array_key_exists($host, $hostHitList) && (time() - $hostHitList[$host]) < 5) {
        //         //     //we hit this host within the last 5 seconds, give them a break and push this url back
        //         //     //on the list as the bottom
        //         //     echo "\nHit host too hard, pushing to back of queue ... \n";
        //         //     $this->urlList[] = $nextURL;
        //         //  else {
        //             if(!$this->checkRobotsTXT($nextURL['href'])) {
        //                 echo "\nURL excluded from MC by robots.txt rule... \n";
        //                 while($nextURL = array_shift($this->urlList)) {
        //                     if($this->checkRobotsTXT($nextURL['href']) !== false) break;
        //                 }
        //             }
        //             curl_setopt($handle, CURLOPT_URL, $nextURL['href']);
        //             curl_multi_add_handle($mc, $handle);
        //             echo "\nAdding new URL {$nextURL['href']} to the crawl ...\n";
        //         // }
                
        //         // $bittech = true;
                
        //         //if $this->urlList is looking a bit low, then request some more URLs from the CrawlManager
        //         //and append them to the end of the list
        //         if(count($this->urlList) == 0) {
        //             echo "\n URL list is dry, requesting new list ...\n";
        //             // sleep(1);
        //             $this->urlList = $this->crawlManager->getURLSet();
        //         }
                
        //         $urlsCrawled++;
        //         echo "\nCrawl URL total: $urlsCrawled ... \n";
        //         $mem = memory_get_usage(true) / 1024 / 1024;
        //         echo "\nMemory used: $mem MB...";
        //     }
            
        //     if($urlsCrawled > 1000) break;
        // } while ($status === CURLM_CALL_MULTI_PERFORM || $active);
        
        // Timer::end('MultiTest');
        
        // print_r($responses);
        // Timer::getTimes();
        // echo "\nCrawled $urlsCrawled URLS (may or may not have been successful)...\n";
        // // print_r($urls);
        // echo "\nFrontier was {$this->crawlManager->frontierSize} URLs long ...";
        // die("foo");
        
        $previousDomain = '';
        $previousLinkCount = 0;
        while($url = array_shift($this->urlList)) {
            
            $url = $url['href'];
            
            $urlData = parse_url($url);
            if($previousLinkCount > 0 && $urlData['host'] == $previousDomain) {
                // delay if this is the same host again
                echo "\nSleeping to save {$urlData['host']}...\n";
                sleep(1);
            }
            
            $fetched = $this->fetch($url);
            
            $this->crawlManager->processNewURLS($fetched['links']);
        
            // store the details of this host, for comparison on the next loop
            $previousDomain = $urlData['host'];
            $previousLinkCount = count($fetched['links']);
            
            if(count($this->urlList) == 0) {
                $this->urlList = $this->crawlManager->getURLSet();
            }
        }
        
        echo "crawler ran out of urls";
    }
    
    public function fetch($url) {
        Timer::start('Crawler::fetch');
        
        //hostname/ip in curl, to avoid constant dns lookup
        //http://stackoverflow.com/questions/9932636/how-to-set-hostname-using-php-curl-for-a-specific-ip
        
        //do this first as if we have a cached version this will save us a head lookup
        if(!$this->checkRobotsTXT($url)) {
            return array(
                'links' => array(),
            );
        }
        
        //if we failed to get a 200 response or this isn't html don't get it
        if(!($url = $this->checkHEAD($url))) {
            return array(
                'links' => array(),
            );
        }
        
        echo "\nFetching URL $url...\n";
        
        $urlParts = parse_url($url);
        $baseURL = $urlParts['scheme'] . '://' . $urlParts['host'] . '/';
        
        //TODO: Don't want to do this every single fetch
        // if($this->checkHEAD($baseURL . 'sitemap.xml', 'application/xml')) {
        //     if($smxml = $this->fetchURI($baseURL . 'sitemap.xml')->response) {
        //         echo "Found sitemap.xml ... ";
        //         // echo $smxml;
        //         $urlset = new \SimpleXMLElement($smxml);
        //         var_dump($urlset->count());
        //         file_put_contents('cache/sitemaps/'.md5($baseURL).'.xml', $smxml);
        //     }
        // }
        
        
        
        $content = $this->fetchURI($url)->response;
        
        Timer::end('Crawler::fetch');
        Timer::getTimes();
        
        $links = $this->getLinks($content, $baseURL, $url);
        // $this->getImages($content);
        
        $this->store($content, $url);
        
        return array(
            'links' => $links,
        );
    }
    
    private function checkHEAD($url, $contentType = 'text/html', $recursionCounter = 0) {
        $url = rtrim($url, '/');
        $url = rtrim($url, '/');
        $url = rtrim($url, '/');
        
        $headResponse = $this->fetchURIHead($url);
        $info = $headResponse->info;
        
        // Input::get();
        
        if($info['http_code'] != 200 || strpos($info['content_type'], $contentType) === false) {
            echo "\nNon 200 OK ({$info['http_code']}) or non $contentType ({$info['content_type']}) at $url...";
            if($info['http_code'] == 301 || $info['http_code'] == 302) {
                //if we hit a 301/302 then return the URL we were sent to after passing it to ourselves
                if($recursionCounter >= self::RECURSION_TOLERANCE) {
                    //in a redirect loop, bail!
                    echo "recursion tolerance reached";
                    // die();
                    return false;
                }
                return $this->checkHEAD($info['url'], $contentType, $recursionCounter+1);
            } else {
                //something else that's not success, return false
                return false;
            }
        } else {
            return $url;
        }
    }
    
    private function checkRobotsTXT($url) {
        $roboto = new RobotsTXT();
        return $roboto->process($url);
    }
    
    private function store($content, $url) {
        $content = time() . "\n$url\n$content";
        // file_put_contents('cache/' . md5($url) . '.html', $content);
        ZIPWriter::save($content, 'cache/documents/' . md5($url) . '.zip');
    }
    
    private function getLinks($data, $baseURL, $currentURL) {
        $dom = new \DOMDocument;
        $dom->loadHTML($data);
        
        $xpath = new \DOMXPath($dom);
        $query = '//a[not(@rel="nofollow")]';
        $nodes = $xpath->query($query);
        
        $numNodes = $nodes->length;
        $links = array();
        for ($i = 0; $i < $numNodes; $i++) {
            $node = $nodes->item($i);
            $href = $node->getAttribute('href');
            
            if(!$href) continue;    //blank URL is no good for us
            
            $urlData = parse_url($href);
            if(!$urlData['scheme']) {
                if(!$urlData['path']) continue;    //we have no scheme, we have no path, what the heck?
                
                // echo $baseURL;
                // var_dump($href);
                // print_r($urlData);
                
                $href = $baseURL . ltrim(str_replace('//', '/', $href), '/');
                $urlData = parse_url($href);
                
                // echo $href;
                // die();
            }
            
            // $href = rtrim($href, '/');
            // $href = rtrim($href, '/');
            // $href = rtrim($href, '/');
            
            if($urlData['scheme'] == 'http' || $urlData['scheme'] == 'https') {
                $text = $node->textContent;
                
                $depth = substr_count($href, '/') - 2;
                
                $host = explode('.', $urlData['host']);
                
                $subdomains = array_slice($host, 0, count($host) - 2 );
                // print_r($subdomains);
                if(count($subdomains) == 0) {
                    $href = str_replace('://', '://www.');
                }
                
                // //restrict crawl to wikipedia urls
                // if(strpos($href, 'en.wikipedia.org') === false) {
                //     continue;
                // }
                
                if($subdomains[0] == 'www') {
                    $tmp = array_shift($subdomains);
                }
                
                //finally remove the hashbang portion of a link (though for AngularJS this could be a valid reference)
                $explosiveURL = explode('#', $href);
                $href = array_shift($explosiveURL);
                
                $depth += count($subdomains);
                
                $links[] = array(
                    'href' => $href,
                    'text' => $text,
                    'depth' => $depth,
                );
            }
        }
        
        //sort by depth
        //we prefer links at the top of a domain
        usort($links, function($a, $b) {
            return $b['depth'] <= $a['depth'];
        });
        
        
        $links = array_map('unserialize', array_unique(array_map('serialize', $links)));
        
        // var_dump($links);
        return $links;
    }
    
    
    
    private function getImages($data) {
        $dom = new \DOMDocument;
        $dom->loadHTML($data);
        
        $xpath = new \DOMXPath($dom);
        $query = '//img';
        $nodes = $xpath->query($query);
        
        $images = array();
        $numNodes = $nodes->length;
        for ($i = 0; $i < $numNodes; $i++) {
            $node = $nodes->item($i);
            $src = $node->getAttribute('src');
            $alt = $node->getAttribute('alt');
            $title = $node->getAttribute('title');
            $images[] = array(
                'src' => $src,
                'alt' => $alt,
                'title' => $title,
            );
        }
        
        // var_dump($images);
        
        return $images;
    }
}