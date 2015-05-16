<?php
namespace Tokenizers;
use Tokenizers\Tokenizer;
use Utils\Timer;

class EnglishTokenizer extends Tokenizer {
    private $_stopWords = array("a", "about", "above", "above", "across", "after", "afterwards", "again", "against", "all", "almost", "alone", "along", "already", "also","although","always","am","among", "amongst", "amoungst", "amount",  "an", "and", "another", "any","anyhow","anyone","anything","anyway", "anywhere", "are", "around", "as",  "at", "back","be","became", "because","become","becomes", "becoming", "been", "before", "beforehand", "behind", "being", "below", "beside", "besides", "between", "beyond", "bill", "both", "bottom","but", "by", "call", "can", "cannot", "cant", "co", "con", "could", "couldnt", "cry", "de", "describe", "detail", "do", "done", "down", "due", "during", "each", "eg", "eight", "either", "eleven","else", "elsewhere", "empty", "enough", "etc", "even", "ever", "every", "everyone", "everything", "everywhere", "except", "few", "fifteen", "fify", "fill", "find", "fire", "first", "five", "for", "former", "formerly", "forty", "found", "four", "from", "front", "full", "further", "get", "give", "go", "had", "has", "hasnt", "have", "he", "hence", "her", "here", "hereafter", "hereby", "herein", "hereupon", "hers", "herself", "him", "himself", "his", "how", "however", "hundred", "ie", "if", "in", "inc", "indeed", "interest", "into", "is", "it", "its", "itself", "keep", "last", "latter", "latterly", "least", "less", "ltd", "made", "many", "may", "me", "meanwhile", "might", "mill", "mine", "more", "moreover", "most", "mostly", "move", "much", "must", "my", "myself", "name", "namely", "neither", "never", "nevertheless", "next", "nine", "no", "nobody", "none", "noone", "nor", "not", "nothing", "now", "nowhere", "of", "off", "often", "on", "once", "one", "only", "onto", "or", "other", "others", "otherwise", "our", "ours", "ourselves", "out", "over", "own","part", "per", "perhaps", "please", "put", "rather", "re", "same", "see", "seem", "seemed", "seeming", "seems", "serious", "several", "she", "should", "show", "side", "since", "sincere", "six", "sixty", "so", "some", "somehow", "someone", "something", "sometime", "sometimes", "somewhere", "still", "such", "system", "take", "ten", "than", "that", "the", "their", "them", "themselves", "then", "thence", "there", "thereafter", "thereby", "therefore", "therein", "thereupon", "these", "they", "thickv", "thin", "third", "this", "those", "though", "three", "through", "throughout", "thru", "thus", "to", "together", "too", "top", "toward", "towards", "twelve", "twenty", "two", "un", "under", "until", "up", "upon", "us", "very", "via", "was", "we", "well", "were", "what", "whatever", "when", "whence", "whenever", "where", "whereafter", "whereas", "whereby", "wherein", "whereupon", "wherever", "whether", "which", "while", "whither", "who", "whoever", "whole", "whom", "whose", "why", "will", "with", "within", "without", "would", "yet", "you", "your", "yours", "yourself", "yourselves", "the");
    
    public function __construct() {
        parent::__construct();
        echo "englsih!";
    }
    
    public function tokenize($text) {
        echo "parsing text";
        $text = preg_replace('/(\s{2,}|[\r\n\t]+)/', ' ', $text);
        $tokens = explode(' ', $text);
        
        // $this->findDates($tokens);
        $preCount = count($tokens);
        
        $stopWords = $this->_stopWords;
        $tokens = array_filter($tokens, function($value) use ($stopWords) {
            return $value AND !in_array($value, $stopWords);
        });
        
        $tokens = array_count_values($tokens);
        
        $tokens = array_filter($tokens, function($value) {
            return $value > 1;
        });
        
        arsort($tokens);
        
        $maxTF = current($tokens);
        $numTerms = count($tokens);
        $tokens = array_map(function($tf) use ($maxTF, $numTerms) {
            // $densityAdj = 1 - (1/$numTerms);
            // return 0.5 + ((0.5 * $tf) / $maxTF);
            return $tf;
        }, $tokens);
        
        // print_r($tokens);
        echo $preCount;
        return $tokens;
    }
    
    private function findDates($tokens) {
        Timer::start('EnglishTokenizer::findDates');
        $tokenCount = count($tokens);
        for($i = 0; $i<$tokenCount; $i++) {
            //check for YYYY MM DD dates
            $date = join(' ', array($tokens[$i], $tokens[$i + 1], $tokens[$i + 2]));
            preg_match('/\d{4}\s\d{1,2}\s\d{1,2}/', $date, $matches);
            if(count($matches) > 0) {
                $possibleDates['dates'][] = $matches[0];
                continue;
            }
            
            //check for WORD YYYY years
            $year = join(' ', array($tokens[$i], $tokens[$i + 1]));
            preg_match('/(\w\s)(\d{4})/', $year, $matches);
            if(count($matches) > 0) {
                $possibleDates['years'][] = $matches[2];
                continue;
            }
        }
        
        $possibleDates['dates'] = array_count_values($possibleDates['dates']);
        ksort($possibleDates['dates']);
        $possibleDates['years'] = array_count_values($possibleDates['years']);
        ksort($possibleDates['years']);
        
        Timer::end('EnglishTokenizer::findDates');
        
        print_r($possibleDates);
    }
    
    private function checkStringForPattern($string, $pattern) {
        preg_match($pattern, $string, $matches);
        if(count($matches) > 0) {
            return $matches;
        } else {
            return false;
        }
    }
}