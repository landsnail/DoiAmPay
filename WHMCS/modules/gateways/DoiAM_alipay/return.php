<?php
# 同步返回页面
# Required File Includes
include("../../../init.php");
include("../../../includes/functions.php");
include("../../../includes/gatewayfunctions.php");
include("../../../includes/invoicefunctions.php");


$gatewaymodule = "DoiAM_alipay"; # Enter your gateway module name here replacing template
$GATEWAY = getGatewayVariables($gatewaymodule);

$url			= $GATEWAY['systemurl'];
$companyname 	= $GATEWAY['companyname'];
$currency		= $GATEWAY['currency'];

if (!$GATEWAY["type"]) die("Module Not Activated"); # Checks gateway module is active before accepting callback

$status = $_GET['status'];    //获取支付宝传递过来的交易状态
$invoiceid = $_GET['out_trade_no']; //获取支付宝传递过来的订单号
$transid = $_GET['trade_no'];       //获取支付宝传递过来的交易号
$amount = $_GET['money'];       //获取支付宝传递过来的总价格
?>
<!DOCTYPE html> 
<html> 
<head> 
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"> 
	<title>支付宝支付接口返回页面</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="./bootstrap.min.css" rel="stylesheet" type="text/css">
	<link href="./style.css" rel="stylesheet" type="text/css">
</head> 
<body>
<div class="container">
	<div class="row">
		<div class="col-xs-10 col-xs-offset-1 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
			<div class="well">
				<div class="header">
					<h1 class="logo"><?php echo $companyname?></h1>
				</div>
				<div class="content">
					<div class="row">
						<div class="col-sm-12">
	<?php if($status == 'success') {?>
							<div class="sa-icon sa-success animate">
								<span class="sa-line sa-tip animateSuccessTip"></span>
								<span class="sa-line sa-long animateSuccessLong"></span>
								<div class="sa-placeholder"></div>
								<div class="sa-fix"></div>
						    </div>
	<?php } else {?>
							<div class="sa-icon sa-error animateErrorIcon">
								<span class="sa-x-mark animateXMark">
									<span class="sa-line sa-left"></span>
									<span class="sa-line sa-right"></span>
								</span>
							</div>
	<?php }?>
						</div>
						<div class="col-sm-12 text-center">
	<?php if($status == 'success') {?>
							<h2>您已成功支付 <?php echo $amount; ?> CNY </h2>
	<?php } else {?>
							<h2>奥...好像那里出错了</h2>
	<?php }?>
						</div>
					</div>
				</div>
				<div class="footer">
	<?php if($status == 'success') {?>
					<p>交易编号：<span><?php echo $transid ?></span></p>
					<p>我们会将确认资料发送至您的信箱。</p>
	<?php } else {?>
					<p class="text-center">貌似是什么地方出了一些问题！</p>
	<?php }?>
					<a href="<?php echo $url ?>/clientarea.php" class="btn btn-lg btn-success btn-block">返回用户中心</a>
				</div>
			</div>
		</div>
	</div>
</div>
</body> 
</html>