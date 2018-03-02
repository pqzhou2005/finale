<?php
/**
 * 多进程
 * @author admin
 */
namespace Finale\Core;

class Settings
{
    private $settings = [
        'daemon' => false,
        'log_file' => '',
        'pid_file' => '',
        'worker_num' => 1,
        'package_max_length'=>65535,
    ];

    private static $instance = null;

    public static function getInstance()
    {
        if(self::$instance === null)
        {
            return new Settings();
        }

        return self::$instance;
    }

    private function __construct()
    {

    }

    public function get($name)
    {
        if(!isset($this->settings[$name]))
        {
            throw new \Exception("no this {$name} setting");
        }

        return $this->settings[$name];
    }

    public function set($name,$value)
    {
        if(!isset($this->settings[$name]))
        {
            throw new \Exception("no this {$name} setting");
        }

        $this->settings[$name] = $value;
        return true;
    }

}
