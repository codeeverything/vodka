<?php

namespace Utils;

class Input {
    static function get() {
        echo "\nPausing. Press enter to continue ... ";
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        echo "\nContinuing ...";
    }
}