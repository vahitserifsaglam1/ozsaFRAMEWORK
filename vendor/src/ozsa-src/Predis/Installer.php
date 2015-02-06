<?php
namespace DB;

class  predisInstaller

{
    protected static $installed = false;

    public function boot()
    {
        try {
            PredisAutoloader::register();
            static::$installed = true;;
        } catch (\Exception $e) {
            static::$installed = false;
            echo $e;
        }
    }

    public function create($configs = null)
    {
        if (static::$installed == false) static::boot();
        if ($configs == null) {
            $configs = require APP_PATH . 'Configs/databaseConfigs.php';
            $configs = $configs['predis'];
        }

        try {

            $redis = new PredisClient($configs);

        } catch (\Exception $e) {
            $redis = $e->getMessage();
        }

            return $redis;
    }


}