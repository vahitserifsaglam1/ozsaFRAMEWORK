<?php

     class plugins {

       /**
       * @class :  plugin class
       * @pluginfolder : plugins folder
       * @framework : OZSAFRAMEWROK
       * @author OzsaFramework
       */

         private $pluginsFolder;


         public function __construct(){

             $this->pluginsFolder = "plugins/";

         }
         public function setPluginFolder($folder){
             $this->pluginsFolder = (strstr("/",$folder)) ? $folder : $folder."/";
         }
         public function installPlugin($pluginName){
              $pluginPath = $this->pluginsFolder.$pluginName;
              include $pluginPath;
              global $data,$db;
              if($data){
                  $yapimci = $data['author'];
                  $site = $data['website'];
                  $versiyon = $data['version'];
                  $file = $data['filename'];
              }
              if(file_exists($pluginPath)){
                  $kontrol = $db->query("SELECT pluginName FROM plugins WHERE pluginName = '$pluginName' ")->rowCount();
                  if(!$kontrol){
                      $ekle = $db->query("INSERT INTO plugins SET status = '1',pluginName = '$pluginName',mainFileName = '$file',yapimci='$yapimci',website = '$site',version = '$versiyon'");
                      return ($ekle) ? true:false;
                  }else{
                      return false;
                  }

              }

         }
         public function checkPlugins(){
           $cek = glob("plugins/*",GLOB_ONLYDIR);
              foreach($cek as $key){
                  $yap =  $this->installPlugin($key);
                  $data[$key] = ($yap) ? true:false;
              }
             return $data;
         }
         public function includePlugins(){
                 global $db;
                 $dondur = $db->query("SELECT mainFileName,pluginName FROM plugins WHERE status = '1'");
                 if($dondur->rowCount()){
                     $cek = $dondur->fetch(PDO::FETCH_OBJ);
                     $name = $cek->mainFileName;
                     $pluginName = $cek->pluginName;
                     $file = $this->pluginsFolder.$name;
                     if(file_exists($file)){
                         include $file;
                     }else{
                         error::newError("$pluginName eklentisi için $file Bulunamadı");
                         return false;
                     }
                 }

         }
         public function deletePlugin($pluginName){
             global $db;
             $delete = $db->query("DELETE FROM plugins WHERE pluginName = '$pluginName'");

             if($delete){
                 return true;
             }else{return false;}
         }
         public function setPlugin($pluginName,$type="active"){
             global $db;
             if($type == "active"){
                 $yap = $db->query("UPDATE plugins SET status = '1' WHERE pluginName = '$pluginName'");
                  return ($yap) ? true:false;
             }else{
                 $yap = $db->query("UPDATE plugins SET status ='0' WHERE pluginName = '$pluginName'");
                 return ($yap) ? true:false;
             }

         }
     }

?>