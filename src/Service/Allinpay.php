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
use Symfony\Component\HttpFoundation\RequestStack;

class Allinpay
{
    // const URL = 'https://syb-test.allinpay.com/apiweb/h5unionpay/unionorder';

    private $httpClient;
    private $logger;
    private $em;
    private $requestStack;
    
    public function __construct(HttpClientInterface $httpClient, LoggerInterface $logger, EntityManagerInterface $em, RequestStack $requestStack)
    {
        $accessKeyId = $_ENV['SMS_ACCESS_KEY_ID'];
        $accessKeySecret = $_ENV['SMS_ACCESS_KEY_SECRET'];

        $this->httpClient = $httpClient;
        $this->logger = $logger;
        $this->signName = $_ENV['SMS_SIGNATURE'];
        $this->em = $em;
        $this->requestStack = $requestStack;
    }
    
    public function createOrder(string $sn, int $amount)
    {
        $request = $this->requestStack->getCurrentRequest();
        $domain = $request->getSchemeAndHttpHost();
        
        $data = [
            'cusid' => $_ENV['ALLINPAY_CUSID'],
            'appid' => $_ENV['ALLINPAY_APPID'],
            'version' => '12',
            'trxamt' => $amount,
            'reqsn' => $sn,
            'randomstr' => bin2hex(random_bytes(16)),
            'signtype' => 'RSA',
            'returl' => $domain . '/order/complete',
            'notify_url' => $domain . '/api/order/notify',
        ];

        $data['sign'] = self::sign($data);

        return $data;
    }
    
    public function refund(string $refund_sn, string $oldtrxid,  int $amount)
    {
        $url = 'https://vsp.allinpay.com/apiweb/tranx/refund';

        $data = [
            'cusid' => $_ENV['ALLINPAY_CUSID'],
            'appid' => $_ENV['ALLINPAY_APPID'],
            'version' => '12',
            'trxamt' => $amount,
            'reqsn' => $refund_sn,
            'oldtrxid' => $oldtrxid,
            'randomstr' => bin2hex(random_bytes(16)),
            'signtype' => 'RSA',
        ];

        $data['sign'] = self::sign($data);

        return $this->httpClient->request('POST', $url, ['body' => $data])->toArray();
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

    public static function ValidSign(array $array)
    {
        $sign =$array['sign'];
        unset($array['sign']);
        ksort($array);
        $bufSignSrc = self::ToUrlParams($array);
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

