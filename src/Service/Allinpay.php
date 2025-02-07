<?php
/**
 * vim:ft=php et ts=4 sts=4
 * @author Al Zee <z@alz.ee>
 * @version
 * @todo
 */

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Doctrine\ORM\EntityManagerInterface;

class Allinpay
{
    // const URL = 'https://syb-test.allinpay.com/apiweb/h5unionpay/unionorder';

    private $httpClient;
    private $logger;
    private $em;
    
    public function __construct(HttpClientInterface $httpClient, LoggerInterface $logger, EntityManagerInterface $em)
    {
        $accessKeyId = $_ENV['SMS_ACCESS_KEY_ID'];
        $accessKeySecret = $_ENV['SMS_ACCESS_KEY_SECRET'];

        $this->httpClient = $httpClient;
        $this->logger = $logger;
        $this->signName = $_ENV['SMS_SIGNATURE'];
        $this->em = $em;
    }
    
    public function createOrder()
    {
        // $url = "https://vsp.allinpay.com/apiweb/h5unionpay/unionorder";
        // $url = 'https://syb-test.allinpay.com/apiweb/h5unionpay/unionorder';
        // H5收银台订单提交接口
        $url = 'https://syb.allinpay.com/apiweb/h5unionpay/unionorder';
        // 统一支付API
        // $url = 'https://vsp.allinpay.com/apiweb/unitorder/pay';

        $sn = strtoupper(str_replace('.', '', uniqid('', true)));
        $rand  = bin2hex(random_bytes(16));
        
        $data = [
            'cusid' => $_ENV['ALLINPAY_CUSID'],
            'appid' => $_ENV['ALLINPAY_APPID'],
            'version' => '12',
            'trxamt' => '10',
            'reqsn' => $sn,
            // 'paytype' => 'W03',
            'randomstr' => $rand,
            'signtype' => 'RSA',
            'returl' => 'https://jz.hbdtjj.com/order/pending',
            'notify_url' => 'https://jz.hbdtjj.com/api/order/notify',
        ];
        $data['sign'] = self::sign($data);
        // $headers[] = 'Accept: application/json';

        // $content = $this->httpClient->request('POST', $url, ['headers' => $headers, 'body' => $data])->toArray();
        // $content = $this->httpClient->request('POST', $url, ['body' => $data])->toArray();
        $resp = $this->httpClient->request('POST', $url, ['body' => $data]);
        $resp->getContent();
        // dump($resp);
        dump($resp->getInfo());
        // dump($resp->getInfo('url'));

        return $resp;
    }
    
    public function sign(array $array)
    {
        ksort($array);
        $bufSignSrc = self::ToUrlParams($array);
        
        // Debug data being signed
        // dump("Raw data to sign: " . $bufSignSrc);
        
        // $private_key = '';

        // Format private key properly
        // $key = "-----BEGIN RSA PRIVATE KEY-----\n" . 
        //        chunk_split($private_key, 64, "\n") .
        //        "-----END RSA PRIVATE KEY-----";
      
        $key = self::getPrivateKey();

        // dump($key);
        // Check if key is valid
        $res = openssl_pkey_get_private($key);
        if (!$res) {
            throw new \RuntimeException('Invalid private key: ' . openssl_error_string());
        }

        // Sign with explicit success check
        $result = openssl_sign($bufSignSrc, $signature, $res, OPENSSL_ALGO_SHA1);
        if ($result === false || empty($signature)) {
            throw new \RuntimeException('Signing failed: ' . openssl_error_string());
        }
        
        // dump("Signature length: " . strlen($signature));
        // dump("Raw signature: " . bin2hex($signature));
        
        openssl_free_key($res);
        return base64_encode($signature);
    }

	public static function ToUrlParams(array $array)
	{
		$buff = "";
		foreach ($array as $k => $v)
		{
			if($v != "" && !is_array($v)){
				$buff .= $k . "=" . $v . "&";
			}
		}
		
		$buff = trim($buff, "&");
		return $buff;
	}

    public static function ValidSign(array $array){
        $sign =$array['sign'];
        unset($array['sign']);
        ksort($array);
        $bufSignSrc = AppUtil::ToUrlParams($array);
        $public_key='MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCm9OV6zH5DYH/ZnAVYHscEELdCNfNTHGuBv1nYYEY9FrOzE0/4kLl9f7Y9dkWHlc2ocDwbrFSm0Vqz0q2rJPxXUYBCQl5yW3jzuKSXif7q1yOwkFVtJXvuhf5WRy+1X5FOFoMvS7538No0RpnLzmNi3ktmiqmhpcY/1pmt20FHQQIDAQAB';	
        $public_key = chunk_split($public_key , 64, "\n");
        $key = "-----BEGIN PUBLIC KEY-----\n$public_key-----END PUBLIC KEY-----\n";
        $result= openssl_verify($bufSignSrc,base64_decode($sign), $key );
        return $result;  
    }

    public static function getPrivateKey() {
        return openssl_get_privatekey(file_get_contents($_ENV['ALLINPAY_PRIVATE_KEY_PATH']));
    }

    public function __destruct()
    {
    }
}

