<?php

/**
 * @author admin
 */
namespace Finale;

use Finale\Core\EventsEmiter;
use Finale\Core\EventsLoop;
use Finale\Core\Settings;
use Finale\Process\Master;
use Finale\Util\PidFile;

!defined('FINALE_ROOT') && define('FINALE_ROOT', dirname(__FILE__) . '/');
require_once FINALE_ROOT.'Util/Autoload.php';

class Process extends EventsEmiter
{
    const STARTING = 1;

    const STOPING = 2;

    const RELOADING = 3;

    private $state;

    private $settings;

    private $pid;

    public function __construct($settings = [])
    {
        $this->settings = Settings::getInstance();
        foreach($settings as $k=>$v)
        {
            $this->settings->set($k,$v);
        }

        if (php_sapi_name() != "cli") {
            exit("only run in command line mode \n");
        }
    }

    /**
     * @throws \Exception
     */
    public function start()
    {
        //是否守护进程
        if($this->settings->get('daemon'))
        {
            $daemon = new \Finale\Util\Daemon();
            $daemon->run();
        }

        //保存pid文件
        $pidFile = new PidFile($this->settings->get('pid_file'));
        $pidFile->save(posix_getpid());

        $this->signal();

        //fork子进程
        $master = new Master($this->settings->get('worker_num'));
        $master->forkWorkers();
    }

    public function stop()
    {

    }

    public function reload()
    {
    }

    public function reOpenLogFile()
    {
    }

    public function tick($ms,$func)
    {
        $eventLoop = EventsLoop::getInstance();
        return $eventLoop->tick($ms,$func);
    }

    public function delTimer($timerid)
    {
        $eventLoop = EventsLoop::getInstance();
        return $eventLoop->delTimer($timerid);
    }

    public function setAttribute($attribute, $value)
    {
        $this->settings->set($attribute,$value);
    }

    public function getAttribute($attribute)
    {
        return $this->settings->get($attribute);
    }

    /**
     * 安装信号
     */
    private function signal()
    {
        pcntl_signal(SIGINT, [$this,'stop']);
        pcntl_signal(SIGTERM, [$this,'stop']);
        pcntl_signal(SIGQUIT, [$this,'stop']);
        pcntl_signal(SIGUSR1, [$this,'reOpenLogFile']);
        pcntl_signal(SIGUSR2, [$this,'reload']);
    }
}
