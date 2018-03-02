<?php
/**
 * 多进程
 * @author admin
 */
namespace Finale\Core;

class EventsEmiter
{
    protected $events;

    /**
     * 注册事件监听器
     * @param $eventName
     * @param $listener
     */
    public function on($eventName,$listener)
    {
        $this->events[$eventName] = $listener;
        return true;
    }

    /**
     * 触发事件
     */
    public function trigger($eventName,$params=[])
    {
        if(isset($this->events[$eventName]))
        {
            return call_user_func_array($this->events[$eventName],$params);
        }
        return true;
    }

}
