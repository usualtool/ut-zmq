<?php
namespace usualtool\Zmq;
class Client{
    private $socket;
    private $mode;
    /**
     * @param string $mode 模式：req, rep, pub, sub, push, pull
     * @param string $dsn  连接地址 (如 tcp://*:5555)
     * @param string $topic 可选：订阅的主题 (仅用于 SUB 模式)
     */
    public function __construct(string $mode, string $dsn, string $topic = ''){
        $context = new ZMQContext();
        $this->mode = $mode;
        switch ($mode) {
            case "req":
                $this->socket = $context->getSocket(ZMQ::SOCKET_REQ);
                break;
            case "rep":
                $this->socket = $context->getSocket(ZMQ::SOCKET_REP);
                break;
            case "pub":
                $this->socket = $context->getSocket(ZMQ::SOCKET_PUB);
                break;
            case "sub":
                $this->socket = $context->getSocket(ZMQ::SOCKET_SUB);
                $this->socket->setSockOpt(ZMQ::SOCKOPT_SUBSCRIBE, $topic);
                break;
            case "push":
                $this->socket = $context->getSocket(ZMQ::SOCKET_PUSH);
                break;
            case "pull":
                $this->socket = $context->getSocket(ZMQ::SOCKET_PULL);
                break;
            default:
                throw new Exception("不支持的 ZMQ 模式: $mode");
        }
        if (strpos($dsn, '*:') !== false) {
            $this->socket->bind($dsn);
        } else {
            $this->socket->connect($dsn);
        }
    }
    /**
     * 发送消息
     */
    public function send($message, $topic = null){
        if (in_array($this->mode, ["req", "rep", "pub", "sub", "push", "pull"])) {
            if ($this->mode ===  "pub" && $topic !== null) {
                $this->socket->send($topic, ZMQ::MODE_SNDMORE);
                return $this->socket->send($message);
            }
            return $this->socket->send($message);
        }
        throw new Exception("当前模式 ($this->mode) 不允许主动发送消息 (如 SUB/PULL)。");
    }
    /**
     * 接收消息
     */
    public function recv(){
        if (in_array($this->mode, ["rep", "sub", "pull", "req"])) {
            // SUB 模式先收主题再收内容
            if ($this->mode === "sub") {
                $topic = $this->socket->recv();
                $msg = $this->socket->recv();
                return ['topic' => $topic, 'message' => $msg];
            }
            return $this->socket->recv();
        }
        throw new Exception("当前模式 ($this->mode) 不允许接收消息 (如 PUB/PUSH)。");
    }
    public function getSocket() {
        return $this->socket;
    }
}
