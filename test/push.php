<?php
//任务分发
use usualtool\Zmq\Client;
$pusher = new Client("push", 'tcp://localhost:5557');
$pusher->send("执行任务1...");