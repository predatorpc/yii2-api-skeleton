<?php

namespace app\modules\v1\controllers;

use \yii\helpers\Json;
use yii\web\Controller;
use devmustafa\amqp\PhpAmqpLib\Message\AMQPMessage;
use MongoDB\Client as Mongo;

class TestController extends Controller
{
    public function actionAccept()
    {
        //Authorize by header
        $raw = file_get_contents("php://input");
        $params = json_decode($raw,true);

        if(!empty($params)) {
            $channel = \Yii::$app->amqp->getChannel();
            $channel->queue_declare(\Yii::$app->params['accept_queue'], false, true, false, false);
            $channel->exchange_declare(\Yii::$app->params['accept_exchg'], \Yii::$app->params['accept_type'], false, true, false);
            $channel->queue_bind(\Yii::$app->params['accept_queue'], \Yii::$app->params['accept_exchg']);
            $channel->basic_publish(new AMQPMessage($raw), \Yii::$app->params['accept_exchg'], false);
            $channel->close();

            if (\Yii::$app->params['mongo']) {
                // add custom param for mongo only
                $params['created_at'] = date("Y-m-d H:i:s",strtotime("NOW"));

                // write to mongo
                $mongo      = new Mongo("mongodb://mongo:27017");
                $db         = $mongo->somedb;
                $collection = $db->mycollection;
                $record     = $collection->insertOne($params);

                if ($record->getInsertedId()) {
                    return Json::encode(['status' => true, 'id' => $record->getInsertedId(), 'data' => ['params' => $params]]);
                } else {
                    return Json::encode(['status' => false, 'data' => ['message' => 'no data inserted']]);
                }
            }
        }

        return false;
    }

    public function actionInfo()
    {
        $client = new Mongo('mongodb://mongo:27017');
        $filter  = [];
        $options = ['limit' => 5,'sort' => ['created_at' => -1]]; // sort by desc date
        $cursor = $client->log->lead->find($filter, $options);

        echo "<pre>";
        var_dump($cursor->toArray());
        exit;
    }
}
