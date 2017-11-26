<?php
class DoiAM{
    public static function sort(&$array){
        ksort($array);
    }
    public static function getsign($array,$key){
        unset($array['sign']);
        self::sort($array);
        $sss=http_build_query($array);
        $sign=hash("sha256",$sss.$key);
        $sign=sha1($sign.hash("sha256",$key));
        return $sign;
    }
    public static function sign($array,$key){
        $array['sign']=self::getSign($array,$key);
        return $array;
    }
    public static function checksign($array,$key){
        $new = $array;
        $new=self::sign($new,$key);
        if(!isset($array['sign'])){
            return false;
        }
        return $array['sign']==$new['sign'];
    }
    public static function post($url, $data = null){
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
    }
}
