<?php

namespace app\controllers;


class SiteController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return '';
    }

    public function actionPing()
    {
        return json_encode([
            'code' => 200,
            'status' => true,
            'message' => 'pong'
        ]);
    }
}
