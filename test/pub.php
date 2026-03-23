<?php
//发布者
use usualtool\Zmq\Client;
$pub = new Client("pub",'tcp://localhost:5556');
$pub->send("内容","主题");
