<?php

namespace Finale;
/**
 * @author admin
 */
!defined('FINALE_ROOT') && define('FINALE_ROOT', dirname(__FILE__) . '/');
require_once FINALE_ROOT.'Util/Autoload.php';

class Server extends Process
{
    private $host;

    private $port;

    public function __construct($host, $port, $setting = [])
    {
        $this->host = $host;
        $this->port = $port;

        parent::__construct($setting);
    }

    public function start()
    {
        //创建服务器并监听端口
        $server = new \Finale\Net\Server($this->host, $this->port);
        $server->listen();

        return parent::start();
    }
}
