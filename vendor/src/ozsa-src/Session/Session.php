<?php
class Session
{
    private static $OzsaIncluded = false;

    private static $sessionType;

    private static $nameHast = 'md5';

    public  static $sessionFolder;

    public static function init($configs)
    {

        self::$sessionType = $configs['type'];
        self::$sessionFolder = APP_PATH.'Stroge/Session';
        if(!file_exists(self::$sessionFolder) ) file::makeDir(self::$sessionFolder);chmod(self::$sessionFolder,0777);
    }

    public static function get($name)
    {
        $type = self::$sessionType;

        $funcname = "getSession".$type;

        return self::$funcname($name);
    }
    public static function set($name,$value,$time = false)
    {
        $type = self::$sessionType;

        $funcname = "setSession".$type;

        return self::$funcname($name,$value,$time);
    }
    public static function delete($name)
    {
        $type = self::$sessionType;

        $funcname = "deleteSession".$type;

        return self::$funcname($name);
    }
    public static function flush()
    {
        $type = self::$sessionType;

        switch ($type)
        {
            case 'Php':
                foreach($_SESSION as $key => $value)
                {
                    unset($_SESSION[$key]);
                }
                break;
            case 'Ozsa':
                $ara = file::scanType(self::$sessionFolder,"ozsa");
                foreach($ara as $key)
                {
                    unlink(self::$sessionFolder."/".$key);
                }
                break;
            case 'Json':
                $ara = file::scanType(self::$sessionFolder,"json");
                foreach($ara as $key)
                {
                    unlink(self::$sessionFolder."/".$key);
                }
                break;
        }
    }
    public static function setSessionJson($name,$value,$time = 1800)
    {

        $time = time()+$time;
        $array = array('time' => $time);
        $array['content'] = $value;
        $array = json_encode($array);

        self::createSesssionFile($name,$array,".json");

    }
    public static function setSessionPhp($name,$value,$time=false)
    {
        $_SESSION[$name] = $value;
    }
    public static function setSessionOzsa($name,$value,$time=false)
    {

        if(!self::$OzsaIncluded) self::init();
        $time = time()+$time;
        $array = array();
        $array['time'] = $time;
        $array['content'] = $value;
        $value = Ozsa::encode($array);

        self::createSesssionFile($name,".ozsa",$value);

    }
    public static function getSessionJson($name)
    {

        $don =  self::readSessionFile($name,".json");
        $value =  json_decode($don);
        $file = self::createFileName($name).".json";
        $filetime = filemtime($file);
        if($filetime>$value->time)
        {
            self::deleteSessionJson($name);
        }else{
            return $value->content;
        }
    }
    public static function getSessionPhp($name)
    {
        if(isset($_SESSION[$name])) return $_SESSION[$name];else return false;
    }
    public static function getSessionOzsa($name)
    {
        if(!self::$OzsaIncluded) self::init();
        return Ozsa::decode(self::readSessionFile($name,".ozsa"));
    }
    public static function deleteSessionJson($name)
    {
        $name = self::createFileName($name);
        $file = self::$sessionFolder."/".$name.".json";
        if(file::check($file))  file::delete($file);else return false;
    }
    public static function deleteSessionPhp($name)
    {
        if(isset($_SESSION[$name])) unset($_SESSION[$name]);else error::newError(" $name diye bir session bulunamadı ");
    }
    public static function deleteSessionOzsa($name)
    {
        $name = self::createFileName($name);
        $file = self::$sessionFolder."/".$name.".ozsa";
        if(file::check($file))  file::delete($file);else return false;
    }
    public static function createSesssionFile($name,$ext,$content)
    {
        $name = self::createFileName($name);

        $file = self::$sessionFolder."/".$name.$ext;

        if(!file_exists($file))
        {
            touch($file);
            chmod($file,0777);
            file::setContent($file,$content);
        }else{
            file::setContent($file,$content);
        }

        return $file;

    }
    public static function readSessionFile($name,$ext)
    {
        $name = self::createFileName($name);

        $file = self::$sessionFolder."/".$name.$ext;

        $oku = file::getContent($file,false);

        if($oku) return $oku['content'];else return false;
    }
    public static function createFileName($name)
    {
        $typ = self::$nameHast;

        return $typ($name);
    }
    public function __desctruct()
    {
      $scan =   glob(APP_PATH."/Stroge/Session/*",GLOB_ONLYDIR);
        foreach($scan as $key)
        {
            unlink(realpath($key));
        }
    }
}