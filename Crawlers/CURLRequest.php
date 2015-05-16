<?php
namespace Crawlers;

abstract class CURLRequest {
    
    public function fetchURIHead($url) {
        $curl = \curl_init();
        
        //do a HEAD request
        curl_setopt($curl, CURLOPT_URL, $url);
        // curl_setopt($curl, CURLOPT_FILETIME, true);
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        $header = curl_exec($curl);
        echo "\n$header\n";
        $info = curl_getinfo($curl);
        echo "\nCURL HEAD info ...";
        // print_r($info);
        curl_close($curl);
        
        $data = new \stdClass();
        $data->response = $header;
        $data->info = $info;
        
        return $data;
    }
    
    public function fetchURI($url) {
        // $writefn = function($curl, $chunk) { 
        //   static $data='';
        //   static $limit = 1024; // in bytes
          
        //   $limit = 5 * 1024;
          
        //   $len = strlen($data) + strlen($chunk);
        //   if ($len >= $limit ) {
        //     $data .= $chunk;
        //     echo strlen($chunk) , ' - bytes grabbed ... ';
        //     print_r(curl_getinfo($curl));
        //     // die();
        //     return -1;
        //   }
        
        //   $data .= $chunk;
        //   return strlen($chunk);
        // };

        $curl = \curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_ENCODING , "gzip");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 2);
        // We need progress updates to break the connection mid-way
        // curl_setopt($curl, CURLOPT_BUFFERSIZE, 1024); // more progress info
        // curl_setopt($curl, CURLOPT_NOPROGRESS, false);
        // curl_setopt($curl, CURLOPT_PROGRESSFUNCTION, function($resouce, $DownloadSize, $Downloaded, $UploadSize, $Uploaded) use ($curl) {
        //     // If $Downloaded exceeds 1KB, returning non-0 breaks the connection!
        //     if($Downloaded > 10 * 1024) {
        //         echo ("Content larger than 1MB!");
        //         return -1;
        //     }
        // });
        // curl_setopt($ch, CURLOPT_RANGE, '0-500');
        // curl_setopt($curl, CURLOPT_BINARYTRANSFER, 1);
        // curl_setopt($curl, CURLOPT_WRITEFUNCTION, $writefn);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        
        $content = curl_exec ($curl);
        
        $info = curl_getinfo($curl);
        curl_close ($curl);
        
        // print_r($info);
        // die();
        
        $data = new \stdClass();
        $data->response = $content;
        $data->info = $info;
        
        return $data;
    }
}