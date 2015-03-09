<?php

 namespace Http;

 use Http\Client\Adapter\Socket;
 use Http\Client\Adapter\Curl;

 class Client
 {

   protected $adapter;

     public function __construct()
     {

      $this->adapter = $adapter = \Desing\Single::make('\Adapter\Adapter','Client');

     }

     public function startSocket( $host, $port )
     {

          $this->adapter->addAdapter( new Socket($host,$port) );

     }

     public function startCurl()
     {

          $this->adapter->addAdapter( new Curl() );

     }

   public function get($name)
   {
        return $this->adapter->$name;
   }


 }