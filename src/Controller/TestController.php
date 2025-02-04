<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Service\Sms;
use App\Service\Allinpay;

#[Route('/test')]
final class TestController extends AbstractController
{
    public function __construct(HttpClientInterface $client, Sms $sms)
    {
        $this->httpClient = $client;
        $this->sms = $sms;
    }

    #[Route('/pay', name: 'app_test_pay')]
    public function index(Allinpay $allinpay): Response
    {
        // $url = 'https://syb-test.allinpay.com/apiweb/unitorder/pay';
        $allinpay->createOrder();

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
