<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\Sms;
use App\Service\Allinpay;

#[Route('/api')]
class ApiController extends AbstractController
{
    public function __construct()
    {
    }

    #[Route('/media_objects', methods: ['POST'])]
    public function upload(Request $request): Response
    {
        $uid = $request->request->get('uid');
        $file = $request->files->get('upload');
        $newName = uniqid() . '-' .  $file->getClientOriginalName();
        // copy($file->getPathname(), 'images/' . $newName);
        $file->move('images/', $newName);
        if ($uid !== null) {
            $em = $this->data->getEntityManager();
            $user = $em->getRepository(User::class)->find($uid);
            $user->setAvatar($newName);
            $em->flush();
        }

        return $this->json(['url' => '/images/' . $newName]);
    }
    
    #[Route('/otp/send', methods: ['POST'])]
    public function feedback(Request $request, Sms $sms): Response
    {
        $params = $request->toArray();

        $phone = isset($params['_phone']) ? $params['_phone'] : null;
        
        if ($phone) {
            $resp = $sms->send($phone);
        } else {
            $resp = ['code' => 'err', 'msg' => '未知错误'];
        }

        return $this->json($resp);
    }

    #[Route('/order/notify', methods: ['POST'])]
    public function order_notify(Request $request): Response
    {
        $params = $request->toArray();
        dump($params);
        
        $resp = ['code' => 0];

        return $this->json($resp);
    }
}
