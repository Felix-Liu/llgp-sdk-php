<?php

namespace Llgp\LlgpSdkPhp;

require './Client/HttpClient.php';
require './Request/LLPayRequest.php';
require './Response/LLPayResponse.php';
require './Utils/SignUtil.php';
require './Config/LLPayConfig.php';

use Llgp\LlgpSdkPhp\Client\HttpClient;
use Llgp\LlgpSdkPhp\Constants\LLPayConstant;
use Llgp\LlgpSdkPhp\Request\LLPayRequest;
use Llgp\LlgpSdkPhp\Config\LLPayConfig;
use Llgp\LlgpSdkPhp\Utils\SignUtil;

final class LLPayClient
{
    private $httpClient;

    private $signUtil;

    private $serverUrl;

    private $merchantPrivateKey;

    private $lianLianPublicKey;

    public function __construct($productionEnv, $merchantPrivateKey, $lianLianPublicKey)
    {
        $this->httpClient = new HttpClient();
        $this->signUtil = new SignUtil();
        if ($productionEnv) {
            $this->serverUrl = LLPayConfig::$prodServerUrl;
        } else {
            $this->serverUrl = LLPayConfig::$sandboxServerUrl;
        }
        $this->merchantPrivateKey = $merchantPrivateKey;
        $this->lianLianPublicKey = $lianLianPublicKey;
    }

    public function execute(LLPayRequest $payRequest)
    {
//        $validateResult = $payRequest->validate();
//        if (!is_null($validateResult)) {
//            $response = new LLPayResponse();
//            $response->code = 400000;
//            $response->message = $validateResult;
//            return $response->toMap();
//        }
        // add sign
        $signText = $this->signUtil->object_to_array($payRequest);
        $signature = $this->signUtil->sign($signText, $this->merchantPrivateKey);
        $header = array(
            'Content-Type: ' . LLPayConstant::CONTENT_TYPE,
            'sign_type: ' . LLPayConstant::CONTENT_TYPE,
            'sign: ' . $signature
        );

        // service request
        $result = null;
        if ($payRequest->httpMethod() == LLPayConstant::HTTP_METHOD_POST) {
            $result = $this->httpClient->post($this->serverUrl, $header, $payRequest);
        } else {
            $result = $this->httpClient->get(
                $this->serverUrl. '?' . $this->signUtil->gen_sign_content($signText), $header);
        }

//        $response = LLPayResponse::fromMap($result['body']);

        // process request result
        $response = $result['body'];
        $headerResponse = $result['header'];
        $code = $response['code'];
        $message = $response['message'];
        if ($code != 200000 || $message != 'Success') {
            return $response;
        }
        $lianLianSign = $headerResponse['sign'][0];
        if (empty($lianLianSign)) {
            $response['sign_verify'] = false;
            return $response;
        }
        $check = $this->signUtil->verify($response, $lianLianSign, $this->lianLianPublicKey);
        $response['sign_verify'] = $check;
        return $response;
    }
}