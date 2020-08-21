<?php
namespace SwoCloud;
use Redis;
use Swoole\Server;
use SwoCloud\Support\Arithmetic;
use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Response as SwooleResponse;
class Dispatcher
{
	/**
	* 连接请求登入
	* 
	* @param Route $route
	* @param SwooleRequest $swooleRequest
	* @param SwooleResponse $swooleResponse
	*/
	public function login(Route $route, SwooleRequest $swooleRequest, SwooleResponse $swooleResponse)
	{
		$data = $swooleRequest->post;
		// 用户账号和密码校验
		// 获取连接的服务器
		$server = \json_decode($this->getIMServer($route), true);
		dd($server, '获取的server');
		$url = $server['ip'].':'.$server['port'];
		// 生成token
		$token = $this->getJwtToken($server['ip'], $data['id'], $url);
		dd($token, '生成的token');
		$swooleResponse->end(\json_encode(['token' => $token,'url' => $url]));
	}
	/**
	* 获取token
	* composer require firebase/php-jwt 来安装jwt
	* @param int $sid 服务器的fd
	* @param int $uid 用户id
	* @param string $url 连接的地址
	* @return string 生成的jwt token
	*/
	protected function getJwtToken($sid, $uid, $url){
		// iss: jwt签发者
		// sub: jwt所面向的用户
		// aud: 接收jwt的一方
		// exp: jwt的过期时间，这个过期时间必须要大于签发时间
		// nbf: 定义在什么时间之前，该jwt都是不可用的
		// iat: jwt的签发时间
		// jti: jwt的唯一身份标识，主要用来作为一次性token,从而回避重放攻击
		$key = "swocloud";
		$time = time();
		$token = [
			"iss" => "http://192.168.186.131",// 可选参数
			"aud" => "http://192.168.186.131",// 可选参数
			"iat" => $time, //签发时间
			"nbf" => $time, //生效时间
			"exp" => $time + 7200, //过期时间
			'data' => [
				'uid' => $uid,
				'name' => 'client'.$time.$sid,// 用户名
				'service_url' => $url
			]
		];
		return \Firebase\JWT\JWT::encode($token, $key);
	}
	/**
	* 根据算法获取连接服务
	* 
	* @param Route $route
	* @return
	*/
	protected function getIMServer(Route $route){
		// 从redis中读取信息
		$arr = $route->getRedis()->smembers($route->getServerKey());
		dd($arr, '从redis中获取的请求列表');
		if (!empty($arr)) {
		// 通过算法从中获取到连接的im-server
		return Arithmetic::{$route->getArithmetic()}($arr);
		}
		dd('获取服务器信息失败', 'getIMServer');
		return false;
	}
}
?>