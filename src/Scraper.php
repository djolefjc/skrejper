<?php

namespace Koren\Skrejper;

use simplehtmldom\HtmlWeb;

class Scraper
{

  const FILE_NAME = "data.csv"; //Give the file a name
  const MAIN_SHOP_PAGE_URL = "https://shop-test.tst/shop-example"; //Inster main shop page link
  const ELEMENT_CLASS = ""; //Insert element link class
  const SHOP_PAGE_URL = "https://shop-test.tst/shop-example/page/"; //Add a shop page url, if there are pages
  const NUMBER_OF_PAGES = "1"; //Add number of pages

  const ELEMENTS = [];

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

    if ($this::SHOP_PAGE_URL != "") {
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
      $row = [
        ['ID','Image']
      ];

      foreach ($lines as $line) {
        $client = new HtmlWeb();
        $line = trim($line);
        $html = $client->load($line);
        
        $product_img = "";
        $img = ".some-class a"; 

        foreach ($html->find($img) as $t) {
          $product_img = $t->href;
        }
        $row[$i]['id'] = $i;
        $row[$i]['img'] = $product_img;
        $i++;
      }
      $fp = fopen($this::FILE_NAME, 'w');

      foreach ($row as $fields) {
        fputcsv($fp, $fields, ',', '"', '');
      }

      fclose($fp);
    }
  }

}