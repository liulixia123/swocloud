<?php
namespace SwoCloud;
use SwoStar\Console\Input;
use Swoole\Server as SwooleServer;
use Swoole\WebSocket\Server as SwooleWebSocketServer;
use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Response as SwooleResponse;
/**
* 1. 检测IM-server的存活状态
* 2. 支持权限认证
* 3. 根据服务器的状态，按照一定的算法，计算出该客户端连接到哪台IM-server，返回给客户端，客户端再去连接到对应的服务端,保存客户端与IM-server的路由关系
* 4. 如果 IM-server宕机，会自动从Redis中当中剔除
* 5. IM-server上线后连接到Route，自动加 入Redis(im-server ip:port)
* 6. 可以接受来自PHP代码、C++程序、Java程序的消息请求，转发给用户所在的IM-server
* 7. 缓存服务器地址，多次查询redis
*
* 是一个websocket
*/
class Route extends Server
{
	public function onOpen(SwooleServer $server, $request) {
		dd("onOpen");
	}
	public function onMessage(SwooleServer $server, $frame) {
		dd('onMessage');
	}
	public function onClose(SwooleServer $ser, $fd) {
		dd("onClose");
	}
	public function onRequest(SwooleRequest $request, SwooleResponse $response){
	}
	protected function initEvent(){
		$this->setEvent('sub', [
		'request' => 'onRequest',
		'open' => "onOpen",
		'message' => "onMessage",
		'close' => "onClose",
		]);
	}
	public function createServer()
	{
		$this->swooleServer = new SwooleWebSocketServer($this->host, $this->port);
		Input::info('WebSocket server 访问 : ws://192.168.186.130:'.$this->port );
	}
}
?>
