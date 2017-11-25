<?php
use WHMCS\Database\Capsule;
# Required File Includes
include("../../../init.php");
include("../../../includes/functions.php");
include("../../../includes/gatewayfunctions.php");
include("../../../includes/invoicefunctions.php");

$gatewaymodule = "DoiAM_alipay"; # Enter your gateway module name here replacing template
$GATEWAY       = getGatewayVariables($gatewaymodule);
if (!$GATEWAY["type"])
    die("Module Not Activated"); # Checks gateway module is active before accepting callback

$order_data                  = $_POST;
$gatewaySELLER_EMAIL         = $GATEWAY['account'];
$gatewaySECURITY_CODE        = $GATEWAY['key'];

$status    = $order_data['status'];         //获取传递过来的交易状态
$invoiceid = $order_data['out_trade_no'];     //订单号
$transid   = $order_data['trade_no'];       //转账交易号
$amount    = $order_data['money'];          //获取递过来的总价格
$fee=0;
if(!da_checksign($_POST,$gatewaySECURITY_CODE)){
    die(json_encode(array('errcode'=>2333)));
    
}


if ($status == 'success') {
    $invoiceid = checkCbInvoiceID($invoiceid, $GATEWAY["name"]);
    checkCbTransID($transid);
    addInvoicePayment($invoiceid, $transid,Capsule::table('tblinvoices')->where('id',$invoiceid)->get()->total,0,$gatewaymodule);//Capsule::table('tblinvoices')->where('id',$invoiceid)->update(['status'=>'Paid']);
    logTransaction($GATEWAY["name"], $_POST, "Successful");
    echo json_encode(['errcode'=>0]);
} else {
    echo 'faild';
}