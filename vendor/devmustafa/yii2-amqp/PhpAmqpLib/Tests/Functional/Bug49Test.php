<?php

namespace devmustafa\amqp\PhpAmqpLib\Tests\Functional;

use devmustafa\amqp\PhpAmqpLib\Connection\AMQPConnection;
use devmustafa\amqp\PhpAmqpLib\Message\AMQPMessage;
use devmustafa\amqp\PhpAmqpLib\Exception\AMQPProtocolException;

class Bug49Test extends \PHPUnit_Framework_TestCase
{
    protected $conn;
    protected $ch;
    protected $ch2;

    public function setUp()
    {
        $this->conn = new AMQPConnection(HOST, PORT, USER, PASS, VHOST);
        $this->ch = $this->conn->channel();
        $this->ch2 = $this->conn->channel();
    }

    public function testDeclaration()
    {
        try {
            $this->ch->queue_declare('pretty.queue', true, true);
            $this->fail('Should have raised an exception');
        } catch (AMQPProtocolException $e) {
            if ($e->getCode() == 404) {
                $this->ch2->queue_declare('pretty.queue', false, true, true, true);
            } else {
                $this->fail('Should have raised a 404 Error');
            }
        }
    }

    public function tearDown()
    {
        $this->ch2->close();
        $this->conn->close();
    }
}
