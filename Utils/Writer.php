<?php

namespace Utils;

class Writer {
    
    static function save($data, $where) {
        file_put_contents($where, $data);
    }
}