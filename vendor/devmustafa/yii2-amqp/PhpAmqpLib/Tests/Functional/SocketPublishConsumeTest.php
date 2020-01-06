<?php

namespace devmustafa\amqp\PhpAmqpLib\Tests\Functional;

use devmustafa\amqp\PhpAmqpLib\Connection\AMQPSocketConnection;

class SocketPublishConsumeTest extends AbstractPublishConsumeTest
{
    protected function createConnection()
    {
        return new AMQPSocketConnection(HOST, PORT, USER, PASS, VHOST);
    }
}
