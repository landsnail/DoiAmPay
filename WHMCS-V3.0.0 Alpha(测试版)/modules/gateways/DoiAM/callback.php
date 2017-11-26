<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
use WHMCS\Database\Capsule;
include("../../../init.php");
include("../../../includes/functions.php");
include("../../../includes/gatewayfunctions.php");
include("../../../includes/invoicefunctions.php");
require_once __DIR__."/DoiAM.php";
$type = $_POST['type'];

$gatewaymodule = ["DoiAM_alipay","DoiAM_wepay","DoiAM_qqpay"][$type];
$GATEWAY       = getGatewayVariables($gatewaymodule);
if (!$GATEWAY["type"])
    die("Module Not Activated");

$order_data                  = $_POST;
$gatewaySELLER_EMAIL         = $GATEWAY['account'];
$gatewaySECURITY_CODE        = $GATEWAY['key'];

$status    = $order_data['errcode'];         //获取传递过来的交易状态
$invoiceid = $order_data['trade_no'];     //订单号
$transid   = $order_data['callback_no'];       //转账交易号
$amount    = $order_data['money'];          //获取递过来的总价格
$fee=0;
if(!DoiAM::checksign($_POST,$gatewaySECURITY_CODE)){
    die(json_encode(array('errcode'=>2333)));
}

if ($status == 0) {
    $invoiceid = checkCbInvoiceID($invoiceid, $GATEWAY["name"]);
    checkCbTransID($transid);
    addInvoicePayment($invoiceid, $transid,Capsule::table('tblinvoices')->where('id',$invoiceid)->get()->total,0,$gatewaymodule);//Capsule::table('tblinvoices')->where('id',$invoiceid)->update(['status'=>'Paid']);
    logTransaction($GATEWAY["name"], $_POST, "Successful");
    echo json_encode(['errcode'=>0]);
} else {
    echo 'faild';
}
