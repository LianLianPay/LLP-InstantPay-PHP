<?php


/* *
 * 功能：连连支付实时付款付款申请接口
 * 版本：1.0
 * 修改日期：2016-11-28
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 */
require_once ("llpay.config.php");
require_once ("lib/llpay_apipost_submit.class.php");
require_once ("lib/llpay_security.function.php");


/**************************请求参数**************************/

//商户付款流水号
$no_order = '20161128165916';

//商户时间
$dt_order = '20161128165916';

//金额
$money_order = '0.01';

//收款人姓名
$acct_name = '李三';

//银行账号
$card_no = '6216261000000000018';

//订单描述
$info_order = 'test测试';

//对私标记
$flag_card = '0';

//服务器异步通知地址
$notify_url = 'http://192.168.110.11:8080/test/tradepayapi/receiveNotify.htm';

//平台来源
$platform = 'test.com';

//版本号
$api_version = '1.0';


//实时付款交易接口地址
$llpay_payment_url = 'https://instantpay.lianlianpay.com/paymentapi/payment.htm';
//需http://格式的完整路径，不能加?id=123这类自定义参数

/************************************************************/

//构造要请求的参数数组，无需改动
$parameter = array (
	"oid_partner" => trim($llpay_config['oid_partner']),
	"sign_type" => trim($llpay_config['sign_type']),
	"no_order" => $no_order,
	"dt_order" => $dt_order,
	"money_order" => $money_order,
	"acct_name" => $acct_name,
	"card_no" => $card_no,
	"info_order" => $info_order,
	"flag_card" => $flag_card,
	"notify_url" => $notify_url,
	"platform" => $platform,
	"api_version" => $api_version
);
//建立请求
$llpaySubmit = new LLpaySubmit($llpay_config);
//对参数排序加签名
$sortPara = $llpaySubmit->buildRequestPara($parameter);
//传json字符串
$json = json_encode($sortPara);    

$parameterRequest = array (
	"oid_partner" => trim($llpay_config['oid_partner']),
	"pay_load" => ll_encrypt($json,$llpay_config['LIANLIAN_PUBLICK_KEY']) //请求参数加密
);
$html_text = $llpaySubmit->buildRequestJSON($parameterRequest,$llpay_payment_url);
//调用付款申请接口，同步返回0000，是指创建连连支付单成功，订单处于付款处理中状态，最终的付款状态由异步通知告知
//出现1002，2005，4006，4007，4009，9999这6个返回码时或者没返回码，抛exception（或者对除了0000之后的code都查询一遍查询接口）调用付款结果查询接口，明确订单状态，不能私自设置订单为失败状态，以免造成这笔订单在连连付款成功了，
//而商户设置为失败,用户重新发起付款请求,造成重复付款，商户资金损失

//对连连响应报文内容需要用连连公钥验签
echo $html_text;
?>
