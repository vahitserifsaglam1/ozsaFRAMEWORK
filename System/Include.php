<?php
  error_reporting(E_ALL);
  include "System/App.php";
  App::IncludeSystemFiles();
  #App::InitSystemClasses();
  $db = new Multidb();
  new bootstrap();



