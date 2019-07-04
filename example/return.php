<?php
$conf = require_once 'config.php';

$alipay = new AlipaySign();

$sign = $_GET['sign'];

$_GET = array_diff_key($_GET, array("sign" => $sign));

$_GET["key"] = $conf["key"];

$_sign = $alipay->getSign($_GET, $conf["key"]);

$payId = $_GET['payId'];//商户订单号
$param = $_GET['param'];//创建订单的时候传入的参数
$type = $_GET['type'];//支付方式 ：微信支付为1 支付宝支付为2
$price = $_GET['price'];//订单金额
$reallyPrice = $_GET['reallyPrice'];//实际支付金额


if ($_sign != $sign) {

    exit("校验失败，支付失败");
}
//此处需要进行确认
$web=new web();
$res=$web->get("../../OrderStatus?payId=$payId");
$json=json_decode($res);
if(isset($json->code)&&$json->code===1){
    //这是交易完成
    $web->get("../../Confirm?payId=$payId&sign=".$alipay->getSign(array("payId"=>$payId), $conf["key"]));
    //继续业务流程
    echo "商户订单号：" . $payId . "<br>自定义参数：" . $param . "<br>支付方式：" . $type . "<br>订单金额：" . $price . "<br>实际支付金额：" . $reallyPrice;
}else if($json->code===3){
    exit("交易已经完成");
}




?>