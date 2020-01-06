yii2-amqp
=========
Yii2 extension enables you to use RabbitMQ queuing with native Yii2 syntax.

## Installation

Via composer

```
$ php composer.phar require devmustafa/yii2-amqp "dev-master"
```

Or add

```
"devmustafa/yii2-amqp": "dev-master"
```

to the ```require``` section of your `composer.json` file.

Also, add the following

```
'amqp' => [
	'class' => 'devmustafa\amqp\components\Amqp',
	'host' => '127.0.0.1',
	'port' => 5672,
	'user' => 'username',
	'password' => 'password',
	'vhost' => '/',
],
```

to the ```components``` section of your `config.php` file.

## How to use

1- Sending:

```
	$exchange = 'exchange';
	$queue = 'queue';
	$dataArray = array('x', 'y', 'z');
	$message = serialize($dataArray);

	Yii::$app->amqp->declareExchange($exchange, $type = 'direct', $passive = false, $durable = true, $auto_delete = false);
	Yii::$app->amqp->declareQueue($queue, $passive = false, $durable = true, $exclusive = false, $auto_delete = false);
	Yii::$app->amqp->bindQueueExchanger($queue, $exchange, $routingKey = $queue);
	Yii::$app->amqp->publish_message($message, $exchange, $routingKey = $queue, $content_type = 'applications/json', $app_id = Yii::$app->name);
```

2- Receiving:

```
	set_time_limit(0);
	error_reporting(E_ALL);

	use devmustafa\amqp\PhpAmqpLib\Connection\AMQPConnection;

	$exchange = 'exchange';
	$queue = 'queue';
	$consumer_tag = 'consumer_1';

	$conn = new AMQPConnection('localhost', 5672, 'username', 'password', '/');
	$ch = $conn->channel();
	$ch->exchange_declare($exchange, 'direct', false, true, false);
	$ch->queue_bind($queue, $exchange);

	function process_message($msg) {
		$body = unserialize($msg->body);
	}

	$ch->basic_consume($queue, $consumer_tag, false, false, false, false, 'process_message');

	function shutdown($ch, $conn) {
		$ch->close();
		$conn->close();
	}

	register_shutdown_function('shutdown', $ch, $conn);

	// Loop as long as the channel has callbacks registered
	while (count($ch->callbacks)) {
		$ch->wait();
	}
```
