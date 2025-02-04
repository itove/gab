<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Service\Sms;

#[Route('/test')]
final class TestController extends AbstractController
{
    public function __construct(HttpClientInterface $client, Sms $sms)
    {
        $this->httpClient = $client;
        $this->sms = $sms;
    }

    #[Route('/pay', name: 'app_test_pay')]
    public function index(): Response
    {
        $url = 'https://syb-test.allinpay.com/apiweb/unitorder/pay';
        $sn = strtoupper(str_replace('.', '', uniqid('', true)));
        $rand  = bin2hex(random_bytes(16));
        
        $data = [
            'cusid' => '5505810078000YD',
            'appid' => '00002811',
            'trxamt' => '10',
            'reqsn' => $sn,
            'paytype' => 'W03',
            'randomstr' => $rand,
            'signtype' => 'RSA',
            'sign' => $sig,
            'notify_url' => 'https://gab.dev.itove.com/notify'
        ];

        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }

    #[Route('/sms', name: 'app_test_sms')]
    public function sms(): Response
    {
        $this->sms->send('13207262011', 'verify');

        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
}
