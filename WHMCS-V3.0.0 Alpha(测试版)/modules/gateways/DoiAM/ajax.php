<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/../../../init.php';
use WHMCS\Database\Capsule as DB;

$invoiceid = $_REQUEST['invoiceid'];

$ca = new WHMCS_ClientArea();

$userid = $ca->getUserID() ;

if($userid == 0){
    exit;
}

$invoice = DB::table("tblinvoices")->find($invoiceid);
if (!($invoice and $invoice->userid == $userid)) {
    die(json_encode(
        [
            'errcode'=>-1,
            'errmsg' => "invalid invoiceid"
        ]
    ));
}
if($invoice->status == "Unpaid"){
    die(json_encode([
        'errcode'=>0,
        'status'=>0
    ]));
}
die(json_encode([
    'errcode'=>0,
    'status'=>1
]));
