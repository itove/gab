<?php
/**
 * vim:ft=php et ts=4 sts=4
 * @author Al Zee <z@alz.ee>
 * @version
 * @todo
 */

namespace App\Service;

use AlibabaCloud\SDK\Dysmsapi\V20170525\Dysmsapi;
use Darabonba\OpenApi\Models\Config;
use AlibabaCloud\SDK\Dysmsapi\V20170525\Models\SendSmsRequest;
use AlibabaCloud\SDK\Dysmsapi\V20170525\Models\QuerySmsTemplateListRequest;
use AlibabaCloud\Tea\Utils\Utils\RuntimeOptions;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Psr\Log\LoggerInterface;
use App\Entity\Conf;
use Doctrine\ORM\EntityManagerInterface;

class Sms
{
    private $client;
    private $logger;
    private $signName;
    private $em;

    public function __construct(LoggerInterface $smsLogger, EntityManagerInterface $em)
    {
        $accessKeyId = $_ENV['SMS_ACCESS_KEY_ID'];
        $accessKeySecret = $_ENV['SMS_ACCESS_KEY_SECRET'];
        $config = new Config([
            "accessKeyId" => $accessKeyId,
            "accessKeySecret" => $accessKeySecret 
        ]);

        $this->client = new Dysmsapi($config);
        $this->logger = $smsLogger;
        $this->signName = $_ENV['SMS_SIGNATURE'];
        $this->em = $em;
    }

    public function getTemplateList($page = 1, $pageSize = 50)
    {
        $opts = new RuntimeOptions([]);
        $querySmsTemplateListRequest = new QuerySmsTemplateListRequest([]);
        $resp = $this->client->querySmsTemplateListWithOptions($querySmsTemplateListRequest, $opts);
        return $resp->body->smsTemplateList;
    }

    public function send($phone, $type = 'verify', $params = [], $cc = false)
    {
        $templateCode = match ($type) {
            'verify' => 'SMS_478475575',
            'paid' => 'SMS_478615591',
            default => 'SMS_478475575',
        };

        if ($type == 'verify') {
            $code = mt_rand(100000, 999999);

            $cache = new RedisAdapter(RedisAdapter::createConnection('redis://localhost'));
            // $cache = new FilesystemAdapter();
            $cache->clear($phone);
            $cache->get($phone, function (ItemInterface $item) use ($code){
                $item->expiresAfter(300);
                return $code;
            });

            $params = ['code' => $code];
        }

        $templateParam = empty($params) ? '' : json_encode($params, JSON_UNESCAPED_UNICODE);

        if ($cc) {
            $list = $this->em->getRepository(Conf::class)->find(1)->getCc();
            array_push($list, $phone);
        } else {
            $list = [$phone];
        }

        foreach ($list as $to) {
            $sendSmsRequest = new SendSmsRequest([
                "phoneNumbers" => $to,
                "signName" => $this->signName,
                "templateCode" => $templateCode,
                "templateParam" => $templateParam
            ]);

            $resp = $this->client->sendSms($sendSmsRequest);
            $this->logger->info('SMS resp: type: {type}, to: {to}, code: {code}, msg: {msg}, templateParam: {templateParam}',
                [
                    'type' => $type,
                    'to' => $to,
                    'code' => $resp->body->code,
                    'msg' => $resp->body->message,
                    'templateParam' => $templateParam
                ]
            );
        }
        
        return ['code' => $resp->body->code, 'msg' => $resp->body->message];
    }
}
