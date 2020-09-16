<?php
namespace SwoCloud\Server;
// ...
class Route extends Server
{
	// ...
	/**
	* route服务器发送信息给其他服务器
	* 
	* @param string $ip 指定服务器的ip
	* @param int $port 指定服务器的端口
	* @param array $data 需要发送的数据
	* @param array $header 设置header
	*/
	public function send($ip, $port, $data, $header = null, $uniqid = null)
	{
		$cli = new Client($ip, $port);
		// 判断是否设置header
		empty($header)?:$cli->setHeaders($header);
		if ($cli->upgrade('/')) {
			$cli->push(json_encode($data));
		}
		// 进行消息的确认
		$this->confirmGo($uniqid, $data, $cli);
	}
}
?>