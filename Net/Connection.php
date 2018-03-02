<?php
/**
 * 
 */
namespace Finale\Net;

use Finale\Core\EventsEmiter;

class Connection extends EventsEmiter
{
    private $eventBufferEvent;

    private $eventbase;

    private $settings;

    public function __construct($fd)
    {
        $eventsLoop = EventsLoop::getInstance();
        $this->eventbase = $eventsLoop->getEventBase();
        $this->settings = Settings::getInstance();

        $this->eventBufferEvent = new \EventBufferEvent($this->eventbase, $fd, \EventBufferEvent::OPT_CLOSE_ON_FREE);

        $this->eventBufferEvent->setCallbacks(
            array($this, "onRead"), NULL,
            array($this, "onEvent"), NULL
        );

        if (!$this->eventBufferEvent->enable(\Event::READ))
        {
            throw new \Exception('eventBufferEvent enable false!');
        }
    }

    public function send($message)
    {
        return $this->eventBufferEvent->output->add($message);
    }

    public function close()
    {
        $this->eventBufferEvent->free();

        $this->trigger('close',[$this]);
    }

    public function onRead($eventBufferEvent, $ctx)
    {
        $input = $eventBufferEvent->getInput();
        $message = $input->read($this->settings->get('package_max_length'));

        $this->trigger('message',[$this,$message]);
    }

    public function onEvent($eventBufferEvent, $events, $ctx)
    {
        if ($events & \EventBufferEvent::ERROR)
        {
            $this->trigger('error',[$this,\EventUtil::getLastSocketErrno(),\EventUtil::getLastSocketError()]);
            $this->close();
        }

        if ($events & \EventBufferEvent::EOF)
        {
            $this->close();
        }
    }

    public function __destruct()
    {
    }
}
