<?php
/**
 * 黛米付 支付接口
 * Version:3.0.0 alpha
 */

require_once __DIR__."/DoiAM/DoiAM.php";
use WHMCS\Database\Capsule;

/**
 * DoiAM 支付宝支付接口 接口配置
 */
function DoiAM_alipay_config(){
    return [
        "FriendlyName"  => [
            "Type"  => "System",
            "Value" => "黛米付 支付宝支付 V3.0.0 Alpha"
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

function DoiAM_alipay_link($arr){
    if($_SERVER['PHP_SELF']!="/viewinvoice.php")
    return '<img style="width: 150px" src="'.$arr['systemurl'].'/modules/gateways/DoiAM/alipay.png" alt="支付宝支付"  />';
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
    $ret->code=explode('<script>',$ret->code)[0];
    $ret->code = str_replace("\"","\\\"",$ret->code);
    $str = <<<HTML
    <div class="alipay">
        <center>
            <a href="#" class="btn btn-success" style="width: auto; " id = "jump_to_alipay">跳转到支付宝</a>
        </center>
    </div>
    <script type="text/javascript" src="//cdn.staticfile.org/jquery/3.2.1/jquery.js"></script>
    <script>
        $("#jump_to_alipay").click(function(){
            $("#jump_to_alipay").after("$ret->code");
            $("#alipaysubmit").submit();
        });
    </script>
HTML;
    return $str;
}
