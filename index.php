<!DOCTYPE html>
<html>
   <head>
      <script type='text/javascript' src='jquery/jquery-1.9.1.min.js'></script>
   </head>
   <body>
      <a href='index.php?start=1'>Scrape</a>
      <?
      require ("scraper-tool.php");
      
      if (isset($_GET['start']))
      {
         $link = mysql_connect('localhost', 'root', 'root');
         if (!$link)
            die('Could not connect: ' . mysql_error());
   
         $db_link = mysql_select_db("stock_scraper", $link);
   
         if (!$db_link)
            die("Cannot use database " . mysql_error());
         
         $result = mysql_query("SELECT COUNT(*) FROM companies AS count");
         $row = mysql_fetch_row($result);

         $start = $_GET['start'];
         if ($start >= $row[0])
            die("Finished Scraping");
         
         $end = ($start < $row[0] - 6) ? $start + 5: $row[0];

         $result = mysql_query("SELECT * FROM companies WHERE company_id >= $start && company_id < $end");
   
         $url_arr = array();
         
         $scraper_tool = new ScraperTool();
         
         while ($row = mysql_fetch_array($result))
         {
            $res = mysql_query("DELETE FROM stocks WHERE company = '" . $row['company_key'] . "'");
            if (!$res)
               die("FAILED TO DELETE ENTRY " . mysql_error());
            $temp = "http://finance.yahoo.com/q/ks?s=" . $row['company_key'] . "+Key+Statistics";
            $page_arr[] = $scraper_tool->scrape($temp);
            $company_arr[] = $row['company_key'];
            $url_arr[] = $temp;
         }
         
         mysql_close($link);
      }
      else
      {
         die("Failed to recieve values for start");
      }
      ?>
      <script type="text/javascript">
         var url_arr = <? echo json_encode($url_arr) ?>;
         var page_arr = <? echo json_encode($page_arr) ?>;
         var company_arr = <? echo json_encode($company_arr) ?>;
         for (var i = 0; i < page_arr.length; i++)
         {
            scrapePage(page_arr[i], company_arr[i]);
         }
         
         function scrapePage(page, company)
         {
            $("body").html(page);
            var val_arr = new Array(company);
            var header_arr = new Array("company");
            $("sup").remove();
            $("table.yfnc_datamodoutline1").each(function()
            {
               $(this).children("tbody").children("tr").children("td").children("table").children("tbody").children("tr").each(function()
               {
                  var header = $(this).children("td.yfnc_tablehead1");
                  var data = $(this).children("td.yfnc_tabledata1");
                  var header = header.text();
                  var data = data.text();
                  if (header != "" && data != "")
                  {
                     header_arr.push(header);
                     val_arr.push(data);
                  }
               });
            });
            $.ajax(
            {
               type : "POST",
               url : "/Stock_Scraper/insert_data.php",
               data :
               {
                  headers : header_arr,
                  values : val_arr
               },
               success : function(msg)
               {
                  console.log(msg);
               },
               error : function(error)
               {
                  console.log(error);
               }
            });
         }
         var start = <? echo $start ?> + 5;
         window.setTimeout(function()
         {
            window.location = "http://localhost/Stock_Scraper/index.php?start=" + start;
         }, 1000);
         
      </script>
   </body>
</html>