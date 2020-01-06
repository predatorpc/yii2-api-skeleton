<?php

namespace devmustafa\amqp\PhpAmqpLib\Tests\Functional;

use devmustafa\amqp\PhpAmqpLib\Connection\AMQPStreamConnection;

class StreamPublishConsumeTest extends AbstractPublishConsumeTest
{
    protected function createConnection()
    {
        return new AMQPStreamConnection(HOST, PORT, USER, PASS, VHOST);
    }
}
