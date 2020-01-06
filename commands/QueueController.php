<?php

/*
 *
 *
 * Main console module to resend leads to CRM
 * predator_pc@29082019
 *
 *
 */

namespace app\commands;

use devmustafa\amqp\PhpAmqpLib\Message\AMQPMessage;
use yii\console\Controller;
use yii\helpers\Json;
use app\modules\v1\models\Api;

class QueueController extends Controller
{
    public const API_URL = 'http://domain.com';         // Api URL
    public const API_AUTH_TOKEN = '';                   // token
    public const API_METHOD = '/v1/remote/method';      // remote method
    private $api;                                       // API Obj

    /*
     * Runtime core action for SupervisorD to keep it up and running
     *
     *
     */
    public function actionIndex()
    {
        // in case we failed to consume
        register_shutdown_function([$this, 'shutdown']);

        $channel = \Yii::$app->amqp->getChannel();
        $consumer = 'accept-' . mt_rand(0,9999);

        $channel->queue_declare(\Yii::$app->params['accept_queue'], false, true, false, false);
        $channel->basic_consume(\Yii::$app->params['accept_queue'], $consumer, false, false, false, false, [$this,'processing']);

        // Loop as long as the channel has callbacks registered
        while (count($channel->callbacks)) {
            $channel->wait();
        }
    }

    /*
     * shutdown channel if we crashed or smth. else like this
     *
     */
    public function shutdown()
    {
        \Yii::$app->amqp->getChannel()->close();
        \Yii::$app->amqp->close();
    }

    /*
     * test consumer publish
     *
     */
    public function actionProducerTest()
    {
        $data = ['code' => 300];
        $properties = ['content_type' => 'application/json', 'delivery_mode' => 2];

        $channel = \Yii::$app->amqp->getChannel();
        $channel->queue_declare(\Yii::$app->params['accept_queue'], false, true, false, false);
        $channel->exchange_declare(\Yii::$app->params['accept_exchg'], \Yii::$app->params['accept_type'], false, true, false);
        $channel->queue_bind(\Yii::$app->params['accept_queue'], \Yii::$app->params['accept_exchg']);
        $channel->basic_publish( new AMQPMessage(json_encode($data), $properties), \Yii::$app->params['accept_exchg'], false);
    }

    /*
     * Main handler to try to pu leads into CRM
     *
     */
    public function processing(AMQPMessage $message)
    {
        $data      = Json::decode($message->body, true);
        $this->api = new Api(self::API_URL, ['application: YOUR_APP_ID', 'device: YOUR_DEVICE_ID', 'token: '.self::API_AUTH_TOKEN]);
        $msg       = json_decode($message->body,true);

        if (is_array($msg)) {
            $data  = $this->api->send(self::API_METHOD, 'post', $msg);

            if (YII_DEBUG) file_put_contents("loquery.log", var_export($data, true) . "\n\n", FILE_APPEND);

            if ($data['status'] && !empty($data['payload'])) {
                $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
            } else {
                $message->delivery_info['channel']->basic_nack($message->delivery_info['delivery_tag']);
            }
        }
    }
}