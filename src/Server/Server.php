<?php
namespace SwoCloud\Server;
use Swoole\WebSocket\Server as SwooleServer;
use SwoCloud\Server\Traits\AckTraits;
abstract class Server
{
use AckTraits;
// ...
}
?>
