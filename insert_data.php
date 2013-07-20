<?
require("query-tool.php");
if (isset($_POST['values']) && isset($_POST['headers']))
{
   $header_arr = $_POST['headers'];
   $value_arr = $_POST['values'];
   $query_tool = new QueryTool();
   $query = $query_tool->makeInsertQuery($header_arr, $value_arr);
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
   $result = mysql_query($query);
   if(!$result)
      die("Failed to execute query: " . mysql_error());
   mysql_close($link);
}
?>
   
