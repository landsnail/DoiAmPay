<?php
use WHMCS\Database\Capsule;
function DoiAM_alipay_config() {
    $configarray = array(
        "FriendlyName"  => array(
            "Type"  => "System",
            "Value" => "DoiAM_支付宝支付--V2.1.4"
        ),
        "account"  => array(
            "FriendlyName" => "商户手机号",
            "Type"         => "text",
            "Size"         => "32",
        ),
        "key" => array(
            "FriendlyName" => "安全检验码",
            "Type"         => "password",
            "Size"         => "32",
        ),
        "mchid" => array(
            "FriendlyName" => "商户号",
            "Type"         => "text",
            "Size"         => "32",
        ),
        "version"=> array(
            "FriendlyName"=>"黛米付V2.1.4接口",
            "Type"=>"yesno",
            
        )
    );

    return $configarray;
}

function DoiAM_alipay_form($params) {
    $n1 = $_SERVER['PHP_SELF'];
    if(stristr($n1,'viewinvoice')){
    }else{
         return '<img style="width: 150px" src="'.$systemurl.'/modules/gateways/DoiAM_alipay/alipay.png" alt="支付宝支付" />';
    }
    $systemurl          = $params['systemurl'];
    $invoiceid          = $params['invoiceid'];
list($price,$rate)= [$params['amount'],1]; 
    $phone            = $params['account'];
    $mchid              = $params['mchid'];
    $asd=[
        'trade'=>$invoiceid,
        'price'=>$price,
        'phone'=>$phone,
        'mchid'=>$mchid,
        "subject"=>$params['description'],
        "body"=>$params['description']
        ];
	$asd=da_sign($asd,$params['key']);
	$result=da_post("https://api.daimiyun.cn/v2/alipay/create",$asd);
	//echo ($result);
	$a=$result;
	$result=json_decode($result,true);
	if($result['errcode']!=0){
	    
	    return "API调用失败".json_encode($result);
	}
	$result['code']=explode('<script>',$result['code'])[0];
	$code = '<div class="alipay" style=""><center><p><div id="alipayimg" style="overflow: hidden;margin-bottom: 5px;"><div class="hidden" id="aliii">'.$result['code'].'</div>';
	$code_ajax = '<a href="#" class="btn btn-success btn-block" style="width:230px" onclick="doAlipay();">前往支付宝进行支付</a></p></center></div>';
	$code_ajax = $code_ajax.'
	<script>
    function doAlipay(){
        document.getElementById("alipaysubmit").submit();
    }
</script>';
	
	$code = $code.$code_ajax;
    $n1 = $_SERVER['PHP_SELF'];
    if(stristr($n1,'viewinvoice')){
        return $code;
    }else{
        return '<img style="width: 150px" src="'.$systemurl.'/modules/gateways/DoiAM_alipay/alipay.png" alt="支付宝支付" />';
    }

}
function DoiAM_alipay_link($params) {
    return DoiAM_alipay_form($params);
}
if(!function_exists("da_sort")){
function da_sort(&$array){
    ksort($array);
}
}
if(!function_exists("da_getsign")){
function da_getsign($array,$key){
    unset($array['sign']);
    da_sort($array);
    $sss=http_build_query($array);
    $sign=hash("sha256",$sss.$key);
    $sign=sha1($sign.hash("sha256",$key));
    return $sign;
}}
if(!function_exists("da_sign")){
function da_sign($array,$key){
    $array['sign']=da_getSign($array,$key);
    return $array;
}}
if(!function_exists("da_checksign")){
function da_checksign($array,$key){
    $new = $array;
    $new=da_sign($new,$key);
    if(!isset($array['sign'])){
        return false;
    }
    return $array['sign']==$new['sign'];
}}
if(!function_exists("da_post")){
function da_post($url, $data = null){
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
	if (!empty($data)){
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	}
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$output = curl_exec($curl);
	curl_close($curl);
	return $output;
}}

//LeoTIME
/****************************************************************
 * 
 *       Author:Leo
 * 
 *       Made for daimiyun.cn
 * 
 * **************************************************************/
if(!function_exists("autogetamount")){
function autogetamount($params){
    $amount=$params['amount'];
    $currencyId=$params['currencyId'];
    $currencys=localAPI("GetCurrencies", [], DoiAM_getAdminname());
    if($currencys['result']=='success' and $currencys['totalresults']>=1){
        
    }else{
        var_dump($currencys);
        throw new \Exception('货币设置错误、API请求错误');
        //如果api请求错误或者货币数量小于1
    }
    //获取货币。
    $currencys=$currencys['currencies']['currency'];
    foreach($currencys as $currency){
        if($currencyId==$currency['id']){
            $from=$currency;
            break;
        }
    }
    if(!$from){
        throw new \Exception("货币错误，找不到起始货币。");
    }
    foreach($currencys as $currency){
        $hb=strtoupper($currency['code']);
        if($hb=='CNY' or $hb=='RMB'){
            $cny=$currency;
            break;
        }
    }
    if(!$cny){
        throw new \Exception("找不到人民币货币，请确认后台货币中存在货币代码为CNY的货币！");
    }
    $rate=$cny['rate']/$from['rate'];
    return [round((double)$rate*$amount,2),round((double)$rate,2)];
}
}
if(!function_exists("DoiAM_getAdminname")){
function DoiAM_getAdminname(){
    $admin = Capsule::table('tbladmins')->first();
    return $admin->username;
}
}