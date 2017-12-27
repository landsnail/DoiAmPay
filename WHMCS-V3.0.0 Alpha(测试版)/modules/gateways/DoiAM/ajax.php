<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/../../../init.php';
use WHMCS\Database\Capsule as DB;

$invoiceid = $_REQUEST['invoiceid'];

$invoice = DB::table("tblinvoices")->find((int)$invoiceid);
if (!($invoice)) {
    die(json_encode(
        [
            'errcode'=>-1,
            'errmsg' => "invalid invoiceid",

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
