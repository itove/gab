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
        $url = 'https://syb-test.allinpay.com/apiweb/h5unionpay/unionorder';

        $sn = strtoupper(str_replace('.', '', uniqid('', true)));
        $rand  = bin2hex(random_bytes(16));
        
        $data = [
            'cusid' => '990440148166000',
            'appid' => '00000003',
            'trxamt' => '10',
            'reqsn' => $sn,
            // 'paytype' => 'W03',
            'randomstr' => $rand,
            'signtype' => 'RSA',
            'returl' => '/order/pending',
            'notify_url' => 'https://gab.dev.itove.com/notify',
        ];
        $data['sign'] = self::sign($data);
        // $headers[] = 'Accept: application/json';

        $content = $this->httpClient->request('POST', $url, ['headers' => $headers, 'body' => $data])->toArray();

        return $content;
    }
    
    public function sign(array $array)
    {
        dump($array);
        ksort($array);
        dump($array);
        $bufSignSrc = self::ToUrlParams($array);
        dump($bufSignSrc);
		$private_key='MIICXAIBAAKBgQDtB6T1D/nh5P/wWumTnj76GVQ6TgkbbfeN6HlXwk5ZcnuN26o2tY1SGnm19DIF/sj2J1iECDifbm0h9EeIsmPctfx0dNKpFKX1t/9LrAT7fap+69MbRR3q4VQneHU9qym9jnOSISjoNOXMPwZjBaITJBbKrdI5a1FyPMyTpSyHMQIDAQABAoGAPZbf6QGGt4iubEDjMoVK7eeI+EFwolz3lzsR1JjbjOhvbFPoraCNIQlaGMpj+STUCQn+OQh91gd2ef0kXUOlKKNXI2qOnAqITy8TEnyYJbGQh3JDx+d6NuUShM2uJ4yICgvsjexwIWpyspocP8mEOotGl6t/MxSJPHYzLRO0esUCQQD+o7zcu1ITyjua8RhSkfjL72zt6p2B+sdI2QhmNxZcbhn22r7njohbTHiuuxbg7+vN3XFCovjOPMkeJB+QnyLrAkEA7kvSheppZt5BwImVoFYRPFVTWBqUSwkQ4ACpuCJxpr5OZlL/jtcgLQBVQBB1iIA+GsAuxKn9WBa0hfyLfR1fUwJBALF4lOyScYXxcNFwLy99JRWdbSH0Xop0qegPu1biFeedpOLzWhIwuMBI7+N36V4kWQhFyeZTh2zV2KX1LzqwbrkCQDqaMPK3/CXNINRtwXtFz0VMIov3NWLinuDHqPVcmyCLipJFdQ22v/XxMAXqRk1EZIGFo7q/p0sjgk+1FMS3FXsCQDy4oPHVXyqbIjcGLr3hGDHBWjucaEboge7/gjJr8abrivq6xUvne0XZsA25wYg1eCZE4BTm1IhrwezLUvixn4c=';
        dump($private_key);
        $private_key = chunk_split($private_key , 64, "\n");
        dump($private_key);
        $key = "-----BEGIN RSA PRIVATE KEY-----\n".wordwrap($private_key)."-----END RSA PRIVATE KEY-----";
        dump($key);
        if(openssl_sign($bufSignSrc, $signature, $key, 'sha256WithRSAEncryption' )){
            dump('success');
        }else{
            dump('fail');
        } 
        dump($signature);
        $sign = base64_encode($signature);//加密后的内容通常含有特殊字符，需要编码转换下，在网络间通过url传输时要注意base64编码是否是url安全的
        dump($sign);
        return $sign;
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

    public function __destruct()
    {
    }
}

