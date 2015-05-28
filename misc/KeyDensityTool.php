<?php

/**
 * Purpose : 
 * 
 * Keyword density is the percentage of times a keyword or phrase appears on 
 * a web page compared to the total number of words on the page. 
 * In the context of search engine optimization keyword density can be used as a 
 * factor in determining whether a web page is relevant to a specified 
 * keyword or keyword phrase.
 * 
 * This class help developers to understand
 * how their pages match with wanted keywords 
 * 
 * The class returns a result with containing
 * Keyword found (single or combined) with their count
 * and the percentage of relevance within the page
 *  
 * @author  : Principe Orazio (orazio.principe@gmail.com)
 * @websites: http://principeorazio.wordpress.com http://www.dbpersister.com
 * @version : 1.0
 * @date    : 17/02/2014
 * 
 * 
 * @license http://www.opensource.org/licenses/lgpl-3.0.html
 * 
 */
class KeyDensityTool
{

    private $uri;
    private $timeout;
    private $deepLenght;
    private $keywordsSeparator;
    private $data;
    private $nohtml_data;
    private $wordCount;
    private $keywords;

    function __construct()
    {
        set_time_limit(0);
        $this->data = array();
    }

    /**
     * get the remote page url
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Set a remote page
     * @param string $uri
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    /**
     * return the content that be parsed
     * 
     * @throws Exception
     * @return string
     */
    public function getData()
    {
        if (empty($this->data)) {
            throw new Exception("Empty content detected");
        }

        return $this->data;
    }

    /**
     * Set the content that be parsed
     * @param unknown $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * Set the timeout to download a remote page
     * @param int $timeout
     */
    public function setTimeout($timeout)
    {
        $this->timeout = (int) $timeout;
    }

    /**
     * Get the timeout used to download a remote page
     * defaults 5 minute
     * 
     * @return number
     */
    public function getTimeout()
    {
        return (empty($this->timeout)) ? (60 * 5) : $this->timeout;
    }

    /**
     * Get the key token used to separate terms inside content
     * default | (pipe)
     * 
     * @return string
     */
    public function getKeywordsSeparator()
    {
        return (empty($this->keywordsSeparator)) ? "|" : $this->keywordsSeparator;
    }

    /**
     * Set the key token used to separate terms inside content
     * @param string $keywords_separator
     */
    public function setKeywordsSeparator($keywords_separator)
    {
        $this->keywordsSeparator = (string) $keywords_separator;
    }

    /**
     * Get how many level use to join terms.
     * Default 3
     * 
     * @return number
     */
    public function getDeepLenght()
    {
        return (empty($this->deepLenght)) ? 3 : $this->deepLenght;
    }

    /**
     * Set how many level must be use to join terms.
     * Ex. 3 give a result like this:
     * 
     * 1 - word
     * 2 - word word
     * 3 - word word word
     * 
     * @param string $deepLenght
     */
    public function setDeepLenght($deepLenght)
    {
        $this->deepLenght = (string) $deepLenght;
    }

    /**
     * Return the number of words count for the document
     * @return int
     */
    public function getWordCount()
    {
        return $this->wordCount;
    }

    /**
     * Return the simple text content
     * without tags and html elements
     * 
     * @return string
     */
    public function getNoHtmlData()
    {
        return $this->nohtml_data;
    }

    /**
     * Return all keywords found
     * 
     * @return array
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * You can set $deepLenght results type to be returned
     * If $deepLenght is false the function will return all
     * keywords founds
     * 
     * @param int|bool $deepLenght
     */
    public function getResults($deepLenght = false)
    {
        if (empty($this->keywords)) {
            throw new Exception("No data processed yet");
        }

        return ($deepLenght !== false) ? @$this->keywords[(int) $deepLenght - 1] : $this->keywords;
    }

    /**
     * Simply print out the result of parsing
     * @return string
     */
    public function __toString()
    {
        return (string) var_export($this->keywords, true) . "\n";
    }

    /**
     * Main method that
     * Execute the parsing and process
     * the content
     */
    public function run()
    {
        if (!empty($this->uri)) {
            $this->readRemoteContent();
        }

        $this->processContent();
    }

    ################# PRIVATE FUNCTIONS #################


    /**
     * Read and store the contents of the remote page
     * 
     * @param type $url
     * @param type $timeout
     * @param type $try_counts
     * @return boolean
     */
    private function readRemoteContent()
    {

        if (!function_exists("curl_init")) {
            throw new Exception("Curl library for php must be installed");
        }

        $timeout = $this->getTimeout();

        $ch = curl_init($this->getUri());
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
        curl_setopt($ch, CURLOPT_NOBODY, 0); // set to 1 to eliminate body info from response
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1); // if necessary use HTTP/1.0 instead of 1.1
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Returns response data instead of TRUE(1)
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // uncomment this line if you get no gateway response. ###
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

        $resp = curl_exec($ch); //execute post and get results
        curl_close($ch);

        $this->setData($resp);

        return true;
    }

    
    
    
    /**
     * chr() with unicode support, made static to call it
     * into preg replacement
     * 
     * @param string $codes
     * @return string
     */
    private static function uchr($codes)
    {
        if (is_scalar($codes)) {
            $codes = func_get_args();
        }
        
        $str = '';
        foreach ($codes as $code) {
            $str.= html_entity_decode('&#' . $code . ';', ENT_NOQUOTES, 'UTF-8');
        }
        
        return $str;
    }
    
    
    /**
     * Confert a complete html page to simple text
     * 
     * @param string $string
     * @return string
     */
    public function htmlToText($string)
    {

        $search = array(
            "'<script[^>]*?>.*?</script>'si", // Strip out javascript
            "'<style[^>]*?>.*?</style>'siU", // Strip style tags properly
            "'<[\/\!]*?[^<>]*?>'si", // Strip out html tags
            "'<![\s\S]*?--[ \t\n\r]*>'si", // Strip multi-line comments including CDATA,
            "'([\r\n])[\s]+'", // Strip out white space
            "'&(quot|#34);'i", // Replace html entities
            "'&(amp|#38);'i",
            "'&(lt|#60);'i",
            "'&(gt|#62);'i",
            "'&(nbsp|#160);'i",
            "'&(iexcl|#161);'i",
            "'&(cent|#162);'i",
            "'&(pound|#163);'i",
            "'&(copy|#169);'i",
            "'&(reg|#174);'i",
            "'&#8482;'i",
            "'&#149;'i",
            "'&#151;'i",
            "'&#(\d+);'e"
        );

        $replace = array(
            " ",
            " ",
            " ",
            " ",
            "\\1",
            "\"",
            "&",
            "<",
            ">",
            " ",
            "&iexcl;",
            "&cent;",
            "&pound;",
            "&copy;",
            "&reg;",
            "<sup><small>TM</small></sup>",
            "&bull;",
            "-",
            "KeyDensityTool::uchr(\\1)"
        );



        $text = preg_replace($search, $replace, $string);
        $text = preg_replace($search, $replace, $text);

        //Removing new lines
        $search = array('@\n@', '@\n\r@', '@\r@');
        $text = preg_replace($search, ' ', $text);

        //Removing no alphanumeric chars
        $text = preg_replace("/[^[:alnum:] ]/", '', $text);

        //Removing multiple spaces
        $text = trim(preg_replace('/\s{2,}/', ' ', $text));

        return $text;
    }

    /**
     * Process the content and build the final result
     * 
     * @throws Exception
     * @return boolean
     */
    function processContent()
    {
        $this->nohtml_data = $this->htmlToText($this->getData());
        if (empty($this->nohtml_data)) {
            throw new Exception("No content to process");
        }

        //Verify the empty content
        if (empty($this->nohtml_data)) {
            throw new Exception("Empty content detected");
        }

        $this->wordCount = str_word_count($this->nohtml_data, 0, '0..9');
        $words = str_word_count($this->nohtml_data, 1, '0..9');

        $keywordsSorted = array();
        $words_length = count($words);
        for ($i = 0; $i < $words_length; $i++)
        {
            for ($xd = 0; $xd < $this->getDeepLenght(); $xd++)
            {
                if (!isset($keywordsSorted[$xd])) {
                    $keywordsSorted[$xd] = "";
                }

                for ($xi = 0; $xi <= $xd; $xi++)
                {
                    if ($i + $xi < $this->wordCount) {
                        $word = $words[$i + $xi];
                        $keywordsSorted[$xd] .= $words[$i + $xi] . " ";
                    }
                }
                $keywordsSorted[$xd] = trim($keywordsSorted[$xd]) . $this->getKeywordsSeparator();
            }
        }


        //Sorting and assign density
        for ($i = 0; $i < $this->getDeepLenght(); $i++)
        {
            $this->keywords[$i] = array_filter(explode($this->getKeywordsSeparator(), $keywordsSorted[$i]));
            $this->keywords[$i] = array_count_values($this->keywords[$i]);
            asort($this->keywords[$i]);
            arsort($this->keywords[$i]);

            //Processing percentage values
            foreach ($this->keywords[$i] as $key => $value)
            {
                $this->keywords[$i][$key] = array("count" => $value, "density" => number_format((100 / $this->wordCount * $value), 2));
            }
        }

        return true;
    }

    ################# END PRIVATE FUNCTIONS #################
}
