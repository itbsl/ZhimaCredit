<?php
/**
 * Created by PhpStorm.
 * User: itbsl
 * Date: 2017/6/19
 * Time: 下午5:28
 */

/**
 * Class AuthenticationAction
 * 芝麻信用实名认证
 */
include('./zmopfile/zmop/ZmopClient.php');
include('./zmopfile/zmop/request/ZhimaCreditAntifraudVerifyRequest.php');

class AuthenticationAction {

    private static $_instance;

    //芝麻信用网关地址
    public $gatewayUrl = "https://zmopenapi.zmxy.com.cn/openapi.do";
    //商户私钥文件
    public $privateKeyFile = "./zmopfile/zmkey/private_key.pem";
    //芝麻公钥文件
    public $zmPublicKeyFile = "./zmopfile/zmkey/public_key.pem";
    //数据编码格式
    public $charset = "UTF-8";
    //芝麻分配给商户的 appId
    public $appId = "1001011";

    //成功匹配字符串
    public $succeedStr = "V_CN_NM_MA";

    //芝麻信用返回的字符串标识对应的中文意思
    protected $identifier = array(
        'V_CN_NA' => '查询不到身份证信息',
        'V_CN_NM_UM' => '姓名与身份证号不匹配',
        'V_CN_NM_MA' => '姓名与身份证号匹配'
    );

    //私有化构造方法
    private function __construct() {

    }

    //私有化克隆方法
    private function __clone() {

    }

    //类方法创建单例对象
    public static function getInstance() {
        if(!self::$_instance instanceof self) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }


    protected function getTransactionId() {
        $str = '';
        $str .= time();
        $alphabet = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
        for($i = 0; $i < 15; $i++) {
            $index = mt_rand(0, 51);
            $str .= $alphabet[$index];
        }
        $str .= mt_rand(1000, 9999);
        return $str;
    }

    public function testZhimaCreditAntifraudVerify($name, $certNo){
        $client = new ZmopClient($this->gatewayUrl,$this->appId,$this->charset,$this->privateKeyFile,$this->zmPublicKeyFile);
        $str = $this->getTransactionId();
        $request = new ZhimaCreditAntifraudVerifyRequest();
        $request->setChannel("apppc");
        $request->setPlatform("zmop");
        $request->setProductCode("w1010100000000002859");// 必要参数
        $request->setTransactionId($str);// 必要参数
        $request->setCertNo($certNo);// 必要参数
        $request->setCertType("IDENTITY_CARD");// 必要参数
        $request->setName($name);// 必要参数
        $response = $client->execute($request);
        //$result = json_encode($response);
        if($response->success && $response->verify_code[0] == $this->succeedStr) {
            return true;
        } else {
            return false;
        }
    }
}

