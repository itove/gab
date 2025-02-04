<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\Sms;

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
        
        $code = 1;

        if ($phone) {
            $sms->send($phone);
            $code = 0;
        }

        $data = [
            'code' => $code,
            'msg' => 'ok',
        ];

        return $this->json($data);
    }
}
