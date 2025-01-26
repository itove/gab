<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class TestController extends AbstractController
{
    public function __construct(HttpClientInterface $client)
    {
        $this->httpClient = $client;
    }

    #[Route('/test', name: 'app_test')]
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
}
