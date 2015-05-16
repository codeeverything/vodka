<?php
namespace Tokenizers;
use Interfaces\ITokenizer;

// namespace Vodka {
    abstract class Tokenizer implements ITokenizer {
        public function __construct() {
            echo "tokenize!";
        }
    }
// }