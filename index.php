<!DOCTYPE html>
<html>
   <head>
      <script type='text/javascript' src='jquery/jquery-1.9.1.min.js'></script>
   </head>
   <body>
      <?
      require ("scraper-tool.php");
      require ("htmlpurifier-4.5.0/library/HTMLPurifier.auto.php");
      require ("query-tool.php");
      $config = HTMLPurifier_Config::createDefault();
      $purifier = new HTMLPurifier($config);
      $contents = file_get_contents("http://finance.yahoo.com/q/ks?s=PH+Key+Statistics");
      $clean_html = $purifier->purify($contents);
      echo $clean_html;
   ?>
<script type='text/javascript'>
   $(document).ready(function()
   {
      var val_arr = new Array();
      var header_arr = new Array();
      var company = "PH";
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
         url : "/stock-scraper/insert_data.php",
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
   });
      </script>
   </body>
</html>