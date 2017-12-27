<?php
/**
 * 黛米付 支付接口
 * Version:3.0.0 alpha
 */

require_once __DIR__."/DoiAM/DoiAM.php";
use WHMCS\Database\Capsule;

/**
 * DoiAM 微信支付接口 接口配置
 */
function DoiAM_wepay_config(){
    return [
        "FriendlyName"  => [
            "Type"  => "System",
            "Value" => "黛米付 微信支付 V3.0.0 Alpha"
        ],
        'mchid' => [
            'Type' => 'text',
            'Size' => "32",
            'FriendlyName' => "商户号",
        ],
        'key' => [
            'Type' => 'text',
            'Size' => "32",
            'FriendlyName' => '安全检验码',
        ],
    ];
}

function DoiAM_wepay_link($arr){
    if($_SERVER['PHP_SELF']!="/viewinvoice.php")
    return '<img style="width: 150px" src="'.$arr['systemurl'].'/modules/gateways/DoiAM/wechat.png" alt="微信支付"  />';
    $postdata = [
        'trade_no'=>$arr['invoiceid'],
        'money'=>$arr['amount'],
        'mchid'=>$arr['mchid'],
        "subject"=>$arr['description'],
        "body"=>$arr['description'],
        'random' => mt_rand(0,99999999),
    ];
    $postdata = DoiAM::sign($postdata,$arr['key']);
    $result = DoiAM::post("https://api.daimiyun.cn/v3/create",$postdata);
    $ret = json_decode($result);
    if(!($ret and $ret->errcode==0)){
        return $result . "API调用失败!";
    }
    $invoiceid= $arr['invoiceid'];
    $str = <<<HTML
    <div class="wepay">
        <center>
            <div id="wepayimg" style="border: 1px solid #AAA;border-radius: 4px;overflow: hidden;margin-bottom: 5px;width: 202px;">
                <img class="img-responsive pad" src="https://www.daimiyun.cn/qrcode/x.php?qr=$ret->code" style="width: 250px; height: 200px;">
            </div>
            <a href="#" class="btn btn-success" style="width: auto; ">使用手机微信扫描上面二维码进行支付</a>
        </center>
    </div>
    <script type="text/javascript" src="//cdn.staticfile.org/jquery/3.2.1/jquery.js"></script>
    <script>
        setInterval(function(){
            $.ajax({
                url:"/modules/gateways/DoiAM/ajax.php",
                data:{
                    invoiceid : $invoiceid,
                },
                dataType:"json",
                success:function(data){
                    if(data.status==1){

                        window.location.href= "$systemurl/viewinvoice.php?id=$invoiceid";
                    }
                }
            });
        },1000);
    </script>
HTML;
    return $str;
}
