<?php
//服务端
use usualtool\Zmq\Client;
$server = new Client("rep",'tcp://localhost:5555');
while(true) {
    $msg = $server->recv();
    $server->send("回复：$msg");
}