<?php
/**
 * 多进程
 * @author admin
 */
namespace Finale\Util;

class PidFile
{
    private $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function save($pid)
    {
        return file_put_contents($this->path,$pid);
    }

    public function getPid()
    {
        if (! file_exists($this->path)) {
            return 0;
        }

        $pid = intval(file_get_contents($this->path));

        if (posix_kill($pid, SIG_DFL)) {
            return $pid;
        } else {
            unlink($this->path);
            return 0;
        }
    }
    
}
