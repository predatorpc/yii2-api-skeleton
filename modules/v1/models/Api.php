<?php

namespace app\modules\v1\models;

class Api
{
    protected $baseUrl;
    protected $defaultHeaders = [];

    public function __construct ($url, array $headers = []) {
        $this->baseUrl        = $url;
        $this->defaultHeaders = $headers;
    }

    public function send ($url, $method = 'get', array $params = []) {
        $url = $this->baseUrl . $url;

        return $this->sendRequest($url, $method, $params, $this->defaultHeaders);
    }

    public function sendRequest ($url, $method = 'get', array $params = [], array $headers = []) {
        $ch = \curl_init($url);
        if (\stripos(PHP_OS, 'WIN') === 0) {
            \curl_setopt($ch, CURLOPT_PROXY, '');
        }

        $method = strtoupper($method);
        switch ($method) {
            case 'POST':
                $headers[] = 'Content-Type: application/json';
                \curl_setopt($ch, CURLOPT_POST, true);
                \curl_setopt($ch, CURLOPT_POSTFIELDS, \json_encode($params));
                break;
        }

        \curl_setopt_array($ch, [
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HEADER         => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_VERBOSE     => true,
            CURLINFO_HEADER_OUT => true
        ]);

        $response = \curl_exec($ch);
        $result   = \substr($response, \curl_getinfo($ch, CURLINFO_HEADER_SIZE)); // crop body
        \curl_close($ch);

        return \json_decode($result, true);
    }
}