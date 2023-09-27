<?php

namespace Llgp\LlgpSdkPhp\Response;

class AliOnlinePayResponse
{
    public $merchant_id;
    public $merchant_order_id;
    public $order_id;
    public $order_status;
    public $order_amount;
    public $order_currency;
    public $due_date;
    public $link_url;
    public $create_time;

    public static function fromMap($map = []) {
        $model = new self();
        if(isset($map['merchant_id'])){
            $model->merchant_id = $map['merchant_id'];
        }
        if(isset($map['merchant_order_id'])){
            $model->merchant_order_id = $map['merchant_order_id'];
        }
        if(isset($map['order_id'])){
            $model->order_id = $map['order_id'];
        }
        if(isset($map['order_status'])){
            $model->order_status = $map['order_status'];
        }
        if(isset($map['order_amount'])){
            $model->order_amount = $map['order_amount'];
        }
        if(isset($map['order_currency'])){
            $model->order_currency = $map['order_currency'];
        }
        if(isset($map['due_date'])){
            $model->due_date = $map['due_date'];
        }
        if(isset($map['link_url'])){
            $model->link_url = $map['link_url'];
        }
        if(isset($map['create_time'])){
            $model->create_time = $map['create_time'];
        }
        return $model;
    }
}