<? 
Class QueryTool
{
   public function __construct()
   {
      date_default_timezone_set("UTC");
   }
   
   public function makeInsertQuery($header_arr, $value_arr)
   {
      $col_names = "";
      $col_values = "";
      $temp_header_arr = array();
      for($i = 0; $i < count($header_arr); $i++)
      {
         $header = $header_arr[$i];
         $value = $value_arr[$i];
         $special_case_arr = $this->checkSpecialCases($header, $value);
         $temp_header = $this->replaceCharacters($special_case_arr[0]);
         if($temp_header != "company")
            $temp_value = $this->translateToNum($special_case_arr[1]);
         else
            $temp_value = $value;
         
         if (in_array($temp_value, $temp_header_arr) === false)
         {
            if ($i > 0)
            {
               $col_names .= ", ";
               $col_values.= ", ";  
            }
            $temp_header_arr[] = $temp_header;
            $col_names .= $temp_header . "";
            $col_values .= "'" . $temp_value . "'";
         }
      }
      $query = "INSERT INTO stocks ($col_names) VALUES ($col_values)";
      return $query;
   }
   
   public function checkSpecialCases($header, $value)
   {
      if(preg_match("/Avg Vol/", $header))
         $header = preg_replace("/\(|\)/", "_", $header);
      else if(preg_match("/Date|Fiscal Year Ends|Most Recent Quarter/", $header))
      {
         $value = Date("Y-m-d", strtotime($value));
      }
      else if(preg_match("/Last Split Factor/", $header))
      {
         $value = str_replace(":", "/", $header);
      }
      else if(strtolower($header) == "float:")
      {
         error_log("float_val");
         $header = "float_val";
      }
      else if(strtolower($header) == "trailing annual dividend yield:" && strpos($value, "%") > 0)
      {
         $header = "trailing_annual_dividend_yield_percent";
      }
      else if(strtolower($header) == "shares short (prior month):")
      {
         $header = "shares_short_prior_month";
      }
      return array($header, $value);
   }
   
   public function replaceCharacters($header)
   {
      $sub_length = strpos($header, "(");
      if($sub_length > 0)
         $header = substr($header, 0, $sub_length);
      $header = trim(strtolower($header));
      $header = preg_replace("/%/", "percent", $header);
      $header = preg_replace('/[^a-zA-Z0-9]/', "_", $header);
      $header = preg_replace('/__/', "_", $header);
      
      $last_under = strrpos($header, "_");
      if($last_under == strlen($header) - 1)
      {
         $header = substr($header, 0, strlen($header) - 1);
      }
      return $header;
   }
   
   public function translateToNum($value)
   {
      if(strpos("/", $value) != FALSE)
         return $value;
      $matches = explode("-", $value);
      if(count($matches) == 3)
         return $value;
      if(stripos($value, 'B'))
         $value = floatval($value)*1000000000;
      if(stripos($value, "M"))
         $value = floatval($value)*1000000;
      if(strpos("/%/", $value))
      {
         $value = str_replace("%", "", $value);
         $value = floatval($value)/100;
      }
      return floatval($value);
   }   
}
?>