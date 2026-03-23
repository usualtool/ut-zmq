<?php
//订阅者
use usualtool\Zmq\Client;
$sub = new Client("sub",'tcp://localhost:5556', '主题');
while(true) {
    $data = $sub->recv();
    echo "收到 {$data['topic']}: {$data['message']}\n";
}