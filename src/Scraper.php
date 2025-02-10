<?php

namespace Koren\Skrejper;

use simplehtmldom\HtmlWeb;

class Scraper
{

  const MAIN_SHOP_PAGE_URL = "http://www.e-tiba.rs/product-category/caj-kafa-topla-cokolada/";
  const ELEMENT_CLASS = ".product-image .woocommerce-LoopProduct-link";
  const NUMBER_OF_PAGES = "";
  const SHOP_PAGE_URL = "http://www.e-tiba.rs/product-category/caj-kafa-topla-cokolada/page/";

  public function init($option)
  {
    if ($option == "0") {
      $this->uno();
    }
    if ($option == "1") {
      $this->uno();
    }
    if ($option == "2") {
      $this->dos();
    }
  }

  private function uno()
  {
    $linkovi = [];

    $client = new HtmlWeb();
    $html = $client->load($this::MAIN_SHOP_PAGE_URL);

    $products = $html->find($this::ELEMENT_CLASS);

    foreach ($products as $p) {
      $linkovi[] = $p->href;
    }

    if ($this::NUMBER_OF_PAGES > 1) {
      for ($i = 1; $i <= $this::NUMBER_OF_PAGES; $i++) {
        $client = new HtmlWeb();
        $html = $client->load($this::SHOP_PAGE_URL . $i);

        $products = $html->find($this::ELEMENT_CLASS);

        foreach ($products as $p) {
          $linkovi[] = $p->href;
        }
      }
    }
    $links = array_unique($linkovi);
    $myfile = fopen("links.txt", "w") or die("Unable to open file!");
    $txt = "";

    foreach ($links as $link) {
      $txt .= $link . "\n";
    }

    fwrite($myfile, $txt);
    fclose($myfile);

    echo "DONE\n";
  }

  private function dos()
  {
    if (filesize('links.txt') != 0) {
      $lines = file('links.txt');
      $i = 1;
      $row = [];

      foreach ($lines as $line) {
        $client = new HtmlWeb();
        $line = trim($line);
        $html = $client->load($line);

        $product_img = "";
        $img = ".woocommerce-product-gallery__image a";

        foreach ($html->find($img) as $t) {
          $product_img = $t->href;
        }
        $row[$i]['img'] = $product_img;
        $i++;
      } 
      //EXPORT U CSV
      $delimiter = ",";
      $filename = "data" . ".csv"; // Create file name
      $f = fopen('memory.txt', 'r+');

      //WOO FIELDS
      // $fields = array('featured_image','post_title','post_category','post_content','post_author','post_date','post_format','comment_status','post_status');
      
      $fields = ['Image'];
      fputcsv($f, $fields, $delimiter);
      foreach ($row as $r) {
        $lineData = [$r['img']];
        fputcsv($f, $lineData, $delimiter);
      }
      fseek($f, 0);
      fpassthru($f);
    }
  }

}