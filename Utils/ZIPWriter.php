<?php

namespace Utils;

class ZIPWriter extends Writer {
    
    static function save($data, $where) {
        $zip = new \ZipArchive();
        $zip->open($where, \ZipArchive::CREATE);
        $zip->addFromString('page.html', $data);
        $zip->close();
    }
}