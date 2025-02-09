<?php

namespace Koren\Skrejper;

use simplehtmldom\HtmlWeb;

class Scraper {

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
  //Inicjializujemo simple html dom i dajemo mu response sa curl-a.
  $client = new HtmlWeb();
  $html = $client->load($this::MAIN_SHOP_PAGE_URL);

  //Tražimo elemente sa određenom klasom koju smo pronašli puten inspecta.

  $products = $html->find($this::ELEMENT_CLASS);

  //Lupujemo kroz elemente i stavljamo linkove u niz
  foreach($products as $p){
  $linkovi[] = $p->href;
  }
  if ($this::NUMBER_OF_PAGES > 1) {
    for($i = 1; $i <= $this::NUMBER_OF_PAGES; $i++){

      //Inicjializujemo simple html dom i dajemo mu response sa curl-a.
      $client = new HtmlWeb();
      $html = $client->load($this::SHOP_PAGE_URL . $i);
      
      //Tražimo elemente sa određenom klasom.
      $products = $html->find($this::ELEMENT_CLASS);
      
      //Lupujemo kroz elemente i stavljamo linkove u niz
      foreach($products as $p){
        $linkovi[] = $p->href;
        }
      }
    }
  //Izbacivanje duplikata.
  $links = array_unique($linkovi);

  //Kreiranje fajla i upis linkova u isti.
  $myfile = fopen("links.txt", "w") or die("Unable to open file!");
  $txt = "";
    
  foreach($links as $link){
  $txt .= $link."\n";
  }

  fwrite($myfile, $txt);
  fclose($myfile);

 echo "DONE\n";
}

private function dos()
{
//Proverava da li je tekstualni dokument sa linkovima prazan
if (filesize('links.txt') != 0){

//Otvara links.txt
$lines = file('links.txt');
//Brojac za varijablu koju kasnije koristimo za export proizvoda u csv.
$i = 1;
//Niz koji koristimo za export proizvoda u CSV
$row = [];

foreach($lines as $line){
  $client = new HtmlWeb();
  $line = trim($line);
  $html = $client->load($line);

  $product_img = "";
  //VARIJABLE ZA POPUNITI
  $img = ".woocommerce-product-gallery__image a";

  //VARIJABLE ZA POPUNITI - END
  //Izvlacenje vrednosti sa stranice.
  foreach($html->find($img) as $t){
  $product_img = $t->href;
  }
//Dodavanje vrednosti u niz koji koristimo za export u csv.
  $row[$i]['img'] = $product_img;

  $i++;
  } //Kraj foreach loop-a linkova
//EXPORT U CSV
$delimiter = ",";
$filename = "data" . ".csv"; // Create file name

// Napravi pointer
$f = fopen('memory.txt', 'r+');

//Postavi hedere za kolone tabele.
//WOO FIELDS
// $fields = array('featured_image','post_title','post_category','post_content','post_author','post_date','post_format','comment_status','post_status');
$fields = ['Image'];
//Izbaci sve podatke u redu i stavi u pointer.
foreach($row as $r){
   $lineData = [$r['img']];
   fputcsv($f, $lineData, $delimiter);
 }
//Vrati se na pocetak fajla.
fseek($f, 0);

// Piši po postavljenom pointeru.
fpassthru($f);
    } 
  }

}