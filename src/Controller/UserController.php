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

class UserController extends AbstractController
{
    #[Route('/api/register', name: 'app_register', methods: ['POST'])]
    /**
     * @OA\Post(
     *   path="/v1/user/update",
     *   summary="Form post",
     *   @OA\RequestBody(
     *     @OA\MediaType(
     *       mediaType="multipart/form-data",
     *       @OA\Schema(
     *         @OA\Property(property="name"),
     *         @OA\Property(
     *           description="file to upload",
     *           property="avatar",
     *           type="string",
     *           format="binary",
     *         ),
     *       )
     *     )
     *   ),
     *   @OA\Response(response=200, description="Success")
     * )
     */
    #[OA\Parameter(
        name: 'email',
        description: 'The field used to order rewards',
        in: 'body',
        schema: new OA\Schema(type: 'string'),
    )]
    #[OA\Parameter(
        name: 'password',
        description: 'The field used to order rewards',
        in: 'query',
        schema: new OA\Schema(type: 'string')
    )]



    public function addUser(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $params = $request->request->all();

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
