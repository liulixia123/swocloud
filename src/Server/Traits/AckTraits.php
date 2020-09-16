<?php
namespace SwoCloud\Server\Traits;
use Co;
use swoole_table;
use Swoole\Coroutine\Http\Client;
use Swoole\Table;
trait AckTraits
{
	/**
	* @var Table
	*/
	protected $table;
	/**
	* 创建共享空间内存
	* 
	*/
	protected function createTable()
	{
		$this->table = new Table(1024);
		// 设置 ack确认码
		$this->table->column('ack', swoole_table::TYPE_INT, 1);
		// 尝试次数
		$this->table->column('num', swoole_table::TYPE_INT, 1);
		$this->table->create();
	}
	/**
	* 确认消息的发送，采用协程的方式
	* 
	* @param string $uniqid 任务唯一id
	* @param array $data 请求动作
	* @param client $client 发送方的客户端
	*/
	protected function confirmGo($uniqid, $data, Client $client)
	{
		go(function() use ($uniqid, $data, $client){
			while (true) {
				Co::sleep(1);
				$ackData = $client->recv(0.2);
				$ack = \json_decode($ackData->data, true);
				// 判断是否确认
				if (isset($ack['method']) && $ack['method'] == 'ack') {
					// 确认则修改
					$this->table->incr($ack['msg_id'], 'ack');
					dd('确认信息', $ack['msg_id']);
				}
				// 查询任务的状态
				$task = $this->table->get($uniqid);
				// 如果任务已经被确认了，或者重试超过了3次之后就会清空任务
				if ($task['ack'] > 0 || $task['num'] >= 3) {
					dd('清空任务', $uniqid);
					$this->table->del($uniqid);
					$client->close();
				break;
				} else {
					$client->push(\json_encode($data));
				}
				// 尝试次数加 1
				$this->table->incr($uniqid, 'num');
				dd('$uniqid 尝试一次');
			}
		});
	}
}