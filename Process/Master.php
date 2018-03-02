<?php
/**
 * 多进程
 * @author admin
 */
namespace Finale\Process;

use Finale\Core\EventsEmiter;

class Master extends EventsEmiter
{
    private $workerNum = 0;

    private $workerMaxNum = 0;

    public function __construct($workerMaxNum)
    {
        $this->workerMaxNum = $workerMaxNum;
    }

    public function forkWorkers()
    {
        $this->_forkWorkers();

        $this->monitorWorkers();
    }

    private function _forkWorkers()
    {
        while($this->workerNum < $this->workerMaxNum)
        {
            $this->forkSingerWorker();
        }
    }

    private function forkSingerWorker()
    {
        $pid = pcntl_fork();
        // For master process.
        if ($pid > 0) {

            $this->workerNum++;

        } // For child processes.
        elseif (0 === $pid) {

            $worker = new Worker();
            $worker->run();

        } else {

            throw new \Exception("fork fail");
        }
    }

    private function monitorWorkers()
    {
        while (1) {
            //调用信号处理函数
            pcntl_signal_dispatch();

            $status = 0;

            //阻塞，直到一个信号的到来或者子进程退出
            $pid    = pcntl_wait($status, WUNTRACED);

            //再次调用（有可能是信号来了）
            pcntl_signal_dispatch();

            //如果是子进程退出
            if ($pid > 0) {

                $this->workerNum --;

                if($this->status !== self::STOPING)
                {
                    $this->_forkWorkers();
                }

            } else {
                if ($this->status === self::STOPING && $this->workerNum<=0 ) {

                    exit();
                }
            }
        }
    }

}
