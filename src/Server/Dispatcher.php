<?php
namespace SwoCloud\Server;
// ..
class Dispatcher
{
	// ..
	/**
	* 路由向所有服务进行广播-》服务器再向连接自己的客户端进行信息发送
	* 
	* @param Route $route
	* @param Server $server
	* @param int $fd 服务器连接的fd
	* @param array $data 服务器发送的数据
	*/
	public function routeBroadcast(Route $route, Server $server, $fd, $data) {
		// 从redis中读取所有服务器的信息
		$ims = $route->getIMServers();
		$token = $this->getJwtToken(0, 0, $route->getHost().":".$route->getPort());
		foreach ($ims as $key => $im) {
			$imInfo = \json_decode($im, true);
			// 这里需要注意，因为我们的server实际上是有jwt的认证，因此route也需要生成jwt的token并发送
			$uniqid = session_create_id();
			$route->send($imInfo['ip'], $imInfo['port'], [
					'data' => [
					'msg' => $data['msg'],
				],
				'method' => 'routeBroadcast',
				'msg_id' => $uniqid
				], [
					'sec-websocket-protocol' => $token
				], $uniqid);
		}
	}
}
?>