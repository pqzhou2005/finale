<?php
/**
 * 多进程
 * @author admin
 */
namespace Finale\Core;

class EventsLoop
{
    private static $instance = null;

    private $eventBase;

    private $timerEvents = [];

    private $signalEvents = [];

    public static function getInstance()
    {
        if(self::$instance === null)
        {
            return new EventsLoop();
        }

        return self::$instance;
    }

    private function __construct()
    {
        $this->eventBase = new \EventBase();
    }

    public function getEventBase()
    {
        return $this->eventBase;
    }

    public function tick($ms,$func)
    {
        $event = \Event::timer($this->eventBase,$func);
        $event->add($ms);
        $this->timerEvents[] = $event;
        return count($this->timerEvents)-1;
    }

    public function delTimer($timerid)
    {
        $event = $this->timerEvents[$timerid];
        $event->del();
    }

    public function signal($signum,$func)
    {
        $event = \Event::signal ( $this->eventBase , $signum , $func );
        $event->add();

        $this->signalEvents[] = $event;
        print_r(serialize($event));
    }

    public function reinit()
    {
        $this->eventBase->reInit();
    }

    public function loop()
    {
        $this->eventBase->loop();
    }

}
