<?php 
 

   class View{
   	  public static function render($path,$allInclude = false)
   	  {
   	  	 $path = "app/Views/".$path.".php";
   	  	  if(file_exists($path))
   	  	  {
              if($allInclude)
              {
                  include "app/Views/header.php";
                  include $path;
                  include "app/Views/footer.php";
              }else{
                  include $path;
              }

   	  	  }else{
   	  	  	error::newError("$path View Dosyası bulunamadı");
   	  	  }
   	  }
   }

?>