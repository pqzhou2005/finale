<?php
namespace Finale\Process;
use Finale\Core\EventsEmiter;
use Finale\Core\EventsLoop;

/**
 * @author admin
 */
class Worker extends EventsEmiter
{
    private $eventLoop;

    public function __construct()
    {
        $this->eventLoop = EventsLoop::getInstance();
    }
    
    public function run()
    {
        //
        register_shutdown_function(__CLASS__.'::checkErrors');

        //信号处理
        $this->resetSignal();

        //标准io reset

        $this->eventLoop->reInit();
        $this->eventLoop->loop();

        throw new \Exception("loop exit");
        exit(404);
    }

    public function stop()
    {

    }

    private function resetSignal()
    {
        pcntl_signal(SIGINT, SIG_IGN);
//        pcntl_signal(SIGUSR1, SIG_IGN);
//        pcntl_signal(SIGUSR2, SIG_IGN);

        $this->eventLoop->signal(SIGQUIT, [$this,'stop']);
//        $eventLoop->signal(SIGUSR1, [$this,'sigusr1Handle']);
//        $eventLoop->signal(SIGUSR2, [$this,'sigusr2Handle']);
    }



    public static function checkErrors()
    {
        $error_msg = 'Worker['. posix_getpid() .'] process terminated with ';
        $errors    = error_get_last();
        if ($errors && ($errors['type'] === E_ERROR ||
                $errors['type'] === E_PARSE ||
                $errors['type'] === E_CORE_ERROR ||
                $errors['type'] === E_COMPILE_ERROR ||
                $errors['type'] === E_RECOVERABLE_ERROR)
        ) {
            $error_msg .= self::getErrorType($errors['type']) . " \"{$errors['message']} in {$errors['file']} on line {$errors['line']}\"";
        } else {
            $error_msg .= 'exit()/die(). Please do not call exit()/die()';
        }
    }

    private static function getErrorType($type)
    {
        switch ($type) {
            case E_ERROR: // 1 //
                return 'E_ERROR';
            case E_WARNING: // 2 //
                return 'E_WARNING';
            case E_PARSE: // 4 //
                return 'E_PARSE';
            case E_NOTICE: // 8 //
                return 'E_NOTICE';
            case E_CORE_ERROR: // 16 //
                return 'E_CORE_ERROR';
            case E_CORE_WARNING: // 32 //
                return 'E_CORE_WARNING';
            case E_COMPILE_ERROR: // 64 //
                return 'E_COMPILE_ERROR';
            case E_COMPILE_WARNING: // 128 //
                return 'E_COMPILE_WARNING';
            case E_USER_ERROR: // 256 //
                return 'E_USER_ERROR';
            case E_USER_WARNING: // 512 //
                return 'E_USER_WARNING';
            case E_USER_NOTICE: // 1024 //
                return 'E_USER_NOTICE';
            case E_STRICT: // 2048 //
                return 'E_STRICT';
            case E_RECOVERABLE_ERROR: // 4096 //
                return 'E_RECOVERABLE_ERROR';
            case E_DEPRECATED: // 8192 //
                return 'E_DEPRECATED';
            case E_USER_DEPRECATED: // 16384 //
                return 'E_USER_DEPRECATED';
        }
        return "";
    }
    
}
