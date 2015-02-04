<?php namespace Ozsa;

    class App
    {
        protected $settings = false;
        private $db = false;
        private $request;
        private $data = null;
        public $validator;
        public $boots;

        public function __construct($pathOptions,$configs)
        {
            $this->settings = array('path' => $pathOptions,
            'configs'=> $configs);
            $this->validateAssets();
            $this->cookieStart();
            $this->sessionStart();
            $this->getRequest();
            #$this->installValidator();

        }
        public function validateAssets()
        {
             $assets = new \App\Assets(true);
        }
       public function setErrorReporting()
       {

           error_reporting($this->settings['configs']['Error']['Reporting']);
           return null;
       }
        public function sessionStart()
        {
            $session_name = session_name();
            if (session_start()) {
                setcookie($session_name, session_id(), null, '/', null, null, true);
            }
            return null;
        }
        public function cookieStart()
        {

            \Cookie::init(require APP_PATH.'Configs/cookieConfigs.php');
            return null;
        }
        public static function urlParse()
        {
            return explode("/",$_GET['url']);
        }
        public function getRequest()
        {


              $configs = $this->settings['path'];
              $appPath =  rtrim(APP_PATH,'/');

              $systemPath =  rtrim(SYSTEM_PATH,'/');

              if(!isset($_GET['url'])) $_GET['url'] = 'index';
              if(strstr($_GET['url'],'.php')) $_GET['url'] = str_replace('.php','',$_GET['url']);

              if(!strstr($_GET['url'],'/')) $_GET['url'] .= "/";

              $ex = explode("/",$_GET['url']);

              define('URL',$_GET['url']);

              @$view = $ex[0];

              if(isset($ex[1]))$function = $ex[1];
              if(isset($ex[2])){
                  unset($ex[0]);
                  unset($ex[1]);
                  $params=$ex;

              }else { $params = array(); }

              $render = new \Router();

              if( $view != $appPath && $view != $systemPath && $_SERVER['REQUEST_URI'] != 'public.php')
              {

                  $path  =  $appPath."/Controller/$view.php";
                  include $path;
                  $class = new $view();
                  if(isset($function))call_user_func_array(array($class,$function),$params);

                 # $render->render($appPath."/Views/".$view.".php",$configs);

              }else{

                  $response = new \Response(404,'bu sayfaya erişim hakınız yok','Lütfen koşarak uzaklaşın');

                  $response->execute();
                  die();
              }
        }
        public function __destruct()
        {
            \Session::flush();
        }

    }


