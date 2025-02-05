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
        $url = 'https://syb.allinpay.com/apiweb/h5unionpay/unionorder';

        $sn = strtoupper(str_replace('.', '', uniqid('', true)));
        $rand  = bin2hex(random_bytes(16));
        
        $data = [
            'cusid' => '660290063002ED6',
            'appid' => '00315248',
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
        // dump($array);
        ksort($array);
        // dump($array);
        $bufSignSrc = self::ToUrlParams($array);
        dump($bufSignSrc);
		$private_key='MIIEpAIBAAKCAQEAtLmCSjRIu/QmJacbDrdng0SVXu4Jmm2HbdIQzYw7aOexdyk1RmgB/fBNbwmmhMO5CfFjjwxGCrBOB3sz14o4R6Hn2q4i2bJdLU7Tqw8pnYJ5HCSFP14z4Zyu/PwoB/bQvUW9EVLZlvgbYyBBnHDypN/NAWcD986FTZ6aUzljEzmv4+j0qjnZE2ChgHENSUOPie6RG+rtqUcVNqnc66I23vJZlKlv5Y1Ptc5eYayWUVqiVXagBMCxCL4OCiAzSrkxYAAQSbJcEL7reIxA/eDQU9zY2zpVc9I6zoa7dD42Bzb5HeJLlBQmgb6rVKQFonRcR7FvZ8B/mp3mWl28Kra8awIDAQABAoIBAE8aB1INUmyZ73x5iNlHI1KMWUjErYVfPXCvClW9dF91UfLTIZNggMayQGJCehUQSdR1SFtbRuj0xCJ4JXfI8ts/nWjU4UIh1LC5GOJ9b3yWmAXeYkgbJmAwoVLv12GtAS5m8Ns9RSnUDMC1ZKJhuYK6xlM/0LfNOAGCUw/sRVYrJGlrZzbwakZoZN3q/lGcPWS3ys/0O3NwjTuzpmgHR5oFnePJbpWiQXuFG9giU6sm5hDjDEv5WOv2oZsOK5l3SOmE72i9C8uf5uaat47dZTU94xMWXB1idnlGmh6WBy1hGlspbmJ54aPkiZbquPjnP0R8W+ugBck486rhTMS+jNkCgYEA2SEgbDV/iTiN64UMG7kO+IZftvcfOQ94ebEoMp+rWm3IXmBzJ/z0aLwEthVX4wW0sIXAyZIhKlewVhRLIqtm0EToyz2ksgvUH1w0xrs+hzwjQ/Ef+WzYjswIaijARc+F0uhU5qypKUannjiYtDwJueod5i9ZJ1G549vS9vq3wG8CgYEA1RP69bmMUCINfD8Y1Uj3ArlwLH4aYWZ//ECOgbOyZTwGgc8YI7OBtx2R3fD6V2CmyBLCXbdxfo8AaDRgmluYOCKUdq3lRFhUF9SuYhZlPbnfr8TCRcoj+/5g6GBiCJGmET4830lsuDFVfecOeCUPbti883wbC6E6WwJd1FmQScUCgYEAuqLD6N+Pcdcf/ntNriLDIJL4gSAoQXbv2sKRx/oBY2iMW7tSIORI/iHndtAfzG+iIj3GOj2WrnvTghpNf06PwKQK6nBhOf365r3uS4i1ta7WrVb9YfvSpePxs7a1lwxLfr/gAqwVd/pYqCMD96DHx3vbGXpHiwmv3JGe5FccTZcCgYEAwIHeuGa82CEL4fb3rqrPUAzNxcTgfKMoenSwy4nYYRIMJvc9rfOd/ByhDs2Kv6q4xAX+yMDVryvviDXaGVsreXv0egy+GDNdNnKWYlQtf8kQyTKQ+pCYVjEKyKdbqrY8PVPnlyw1J2ya+rboIbAJ83GptKmpnaY6nMLUluecLqkCgYBtGmdz83eBEykqWf/sqe9kjSr6EUwCXhjdHF5DHsiIXMpCWdBKPsIkPSBYOLBp+pIcs8gHfNY04tLSHMXnAOIEhsINf8AkU2e+6ryazHOl00dRgdyPc5IHvL2bMiE3EAWnuScJAUEA0DWYq2C6HuOU2374JhyGyulSWN10WsVQ4Q==';
        // dump($private_key);
        $private_key = chunk_split($private_key , 64, "\n");
        // dump($private_key);
        $key = "-----BEGIN RSA PRIVATE KEY-----\n".wordwrap($private_key)."-----END RSA PRIVATE KEY-----";
        // dump($key);
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

