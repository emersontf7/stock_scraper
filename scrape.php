<?
   require ("htmlpurifier-4.5.0/library/HTMLPurifier.auto.php");

   if (isset($_POST['url']))
   {
      $config = HTMLPurifier_Config::createDefault();
      $purifier = new HTMLPurifier($config);
      $contents = file_get_contents($_POST['url']);
      $clean_html = $purifier->purify($contents);
      echo $clean_html;
   }

?>