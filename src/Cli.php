<?php 

namespace Koren\Skrejper;

use Scrape;

class Cli {

    /**
     * 1 - Run first step for websites with pagination
     * 2 - Run first step for websites without pagination
     * 3 - Run second step
     */
    protected $options = ["UNO", "UNO MAS", "DOS"];

    /**
     * Display option menu. 
     */
    public function selectOption()
    {
        foreach($this->options as $key => $option) {
            echo "$key $option\n";
        }

        $selected = readLine("Please select an option(0,1,2):");
        if (isset($this->options[$selected])) {
            $scraper = new Scraper();
            $time_start = microtime(true); 
            $scraper->init($selected);
            echo "/n---/nTotal execution time in seconds: " . (microtime(true) - $time_start) . '/n';
        } 
    }
}