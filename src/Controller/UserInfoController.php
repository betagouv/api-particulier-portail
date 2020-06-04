<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class UserInfoController extends AbstractController
{
    /**
     * @var NormalizerInterface
     */
    private $normalizer;

    public function __construct(NormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    /**
     * @Route("/oauth/userinfo")
     */
    public function userInfoAction()
    {
        $serializedUser = $this->normalizer->normalize($this->getUser(), null, [
            'groups' => 'grafana'
        ]);
        return new JsonResponse($serializedUser);
    }
}
