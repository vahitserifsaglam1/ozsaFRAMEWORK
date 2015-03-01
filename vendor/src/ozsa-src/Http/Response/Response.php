<?php


     namespace Http;

     use Http\Response\Factory\ResponseFactory as Factory;

     use Symfony\Component\HttpFoundation\Response as Res;

     /**
      * Class Response
      * @package Http
      *  use Smyfony Http packpage
      */

     class Response
     {

         /**
          * @var Factory
          *
          */
          public $factory;

         /**
          * @param string $content
          * @param int $status
          * @param array $headers
          *
          *  starter function.
          *
          */

          public function __construct($content = '', $status = 200, array $headers = array())
          {

               $this->factory = new Factory( new Res(),new \View(),\Desing\Single::make('\Redirect'));

              if($content !== '')
              {

                  $this->factory->make($content, $status , $headers );


              }

          }

         /**
          * @param $name
          * @param $params
          * @return mixed
          *  if called method not exists this class , called the factory Class
          */
         public function __call($name,$params)
         {

           return call_user_func_array(array($this->factory,$name),$params);

         }

         public static function __callStatic($name,$params)
         {

             $s = new static();

             return call_user_func_array(array($s->factory,$name),$params);

         }

     }
