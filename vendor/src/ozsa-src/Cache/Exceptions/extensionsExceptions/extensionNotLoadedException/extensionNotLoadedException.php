<?php

 namespace Cache\Exceptions\extensionsExceptions;


 class extensionNotLoadedException extends  \Exception
 {


      public function __construct(  $message = ' ')
      {

          $this->message = $message;

      }

 }