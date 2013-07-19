<?
   $handle = @fopen("symbols.txt", "r");
   $stock_symbol_arr;
   if ($handle)
   {
      while (($buffer = fgets($handle)) !== false)
      {
         $stock_symbol_arr = explode("\r", $buffer);
      }
      if (!feof($handle))
      {
         echo "Error: unexpected fgets() fail\n";
      }
      fclose($handle);
   }
   
   $link = mysql_connect('localhost', 'root', 'root');
   if (!$link)
   {
      die('Could not connect: ' . mysql_error());
   }
   echo 'Connected successfully';
   $db_link = mysql_select_db("stock_scraper", $link);
   if(!$db_link)
   {
      die("Cannot use database " . mysql_error());
   }
   foreach($stock_symbol_arr as $symbol)
   {
      $query = "INSERT INTO companies (company_key) VALUES ('$symbol')";
      $result = mysql_query($query);
   }   
   mysql_close($link);
?>