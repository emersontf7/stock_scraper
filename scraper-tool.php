<? 
Class ScraperTool
{
   public $url;
   private $contents;
   public function __construct()
   {
   }
   public function getFileContents()
   {
      $this->contents = file_get_contents($url);
      return $this->contents;
   }
   public function getNextUrl()
   {
      
   }
}
?>
