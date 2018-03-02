<?php
/**
 * @author admin
 */
namespace Finale\Util;

class Daemon
{
    public function __construct()
    {
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function run()
    {
        // 在后台运行。
        $pid = pcntl_fork();
        if (- 1 === $pid) {
            throw new \Exception('fork fail');
        } elseif ($pid > 0) {
            exit(0);
        }
        
        // 脱离控制终端，登录会话和进程组
        if (- 1 === posix_setsid()) {
            throw new \Exception("set sid fail");
        }
        
        // 防止重新获取控制台，生成第二子进程，摆脱组长身份
        $pid = pcntl_fork();
        if (- 1 === $pid) {
            throw new \Exception("fork fail");
        } elseif (0 !== $pid) {
            exit(0);
        }
        
        chdir('/');
        
        // 关闭打开的文件描述符
        fclose(STDIN);
        fclose(STDOUT);
        fclose(STDERR);
        
        // 重设文件创建掩模 进程从创建它的父进程那里继承了文件创建掩模。它可能修改守护进程所创建的文件的存取位。为防止这一点，将文件创建掩模清除
        umask(0);

        return true;
    }
}
