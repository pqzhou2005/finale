<?php
namespace Finale\Net;
/**
 * @author admin
 */
use Finale\Core\EventsEmiter;
use Finale\Core\EventsLoop;


class Server extends EventsEmiter
{
    private $host;

    private $port;
    
    private $eventBase;
    
    private $connections = [];
    
    private $socket;
    
    public function __construct($host, $port)
    {
        $this->host = $host;
        $this->port = $port;

        $eventsLoop = EventsLoop::getInstance();
        $this->eventBase =$eventsLoop->getEventBase();

    }
    
    public function __destruct() 
    {
        foreach ($this->connections as &$c) $c = NULL;
    }
    
    /**
     * 绑定并监听端口
     * @throws \Exception
     */
    public function listen()
    {
        $this->socket = stream_socket_server("tcp://{$this->host}:{$this->port}", $errno, $errmsg, STREAM_SERVER_BIND | STREAM_SERVER_LISTEN);
        if(!$this->socket)
            throw new \Exception($errmsg);
        
        stream_set_blocking($this->socket, 0);

        new \EventListener($this->eventBase,array($this, "onConnect"), $this->eventBase,\EventListener::OPT_CLOSE_ON_FREE | \EventListener::OPT_REUSEABLE,-1,$this->socket);

        return true;
    }

    public function onConnect($listener, $fd, $address, $ctx)
    {
        $connection = new Connection($fd);

        $this->connections[] = $connection;

        $this->trigger('connect',[$connection]);
    }

    public function getConnections()
    {
        return $this->connections;
    }

}
