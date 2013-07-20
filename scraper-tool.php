<?
require ("htmlpurifier-4.5.0/library/HTMLPurifier.auto.php");

Class ScraperTool
{
   public $purifier;
   
   public function __construct()
   {
      $this->purifier = new HTMLPurifier(HTMLPurifier_Config::createDefault());
   }

   public function scrape($url)
   {
      $contents = file_get_contents($url);
      $clean_html = $this->purifier->purify($contents);
      return $clean_html;
   }

}
?>
