<?php 

$succ = 0;
for($i=0;$i<1000;$i++)
{
    $ret = send_message('192.168.40.128','2500','message to send...');

    if($ret) $succ++;
}
echo $succ;

//自定义函数，发送信息
function send_message($ipserver,$portserver,$message)
{
  $fp=stream_socket_client("tcp://$ipserver:$portserver", $errno, $errstr);
  if(!$fp)
  {
    echo "erreur : $errno - $errstr<br />n";
    return false;
  }
  else
  {
    fwrite($fp,"$message\n");
    $messagex =  fread($fp,1024);

    fclose($fp);
    return trim($messagex)==$message;

  }
}
