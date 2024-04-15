<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
//use OpenApi\Attributes as OA;
use OpenApi\Annotations as OA;

/**
 *
 */
class UserController extends AbstractController
{
    /**
     * @param Request $request
     * @param UserPasswordHasherInterface $passwordHasher
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/api/register', name: 'app_register', methods: ['POST'])]
     public function addUser(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $params = json_decode($request->getContent(),true);
        if (!isset($params['email']) || !isset($params['password'])) {
            return new Response(
                json_encode(array('message' => 'Invalid data')),
                400,
                array('content-type' => 'application/json')
            );
        }

        $user = new User();
        $user->setEmail($params['email']);
        $passwordHashed = $passwordHasher->hashPassword($user, $params['password']);
        $user->setPassword($passwordHashed);

        try {
            $entityManager->persist($user);
            $entityManager->flush();
        } catch (\Exception $e) {
            return new Response(
                json_encode(array('message' => $e->getMessage())),
                500,
                array('content-type' => 'application/json')
            );
        }

        return new Response(
            json_encode(array('message' => 'User is created')),
            201,
            array('content-type' => 'application/json')
        );
    }
}
