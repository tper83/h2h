<?php

namespace App\Controller\Api;

use App\Docs\MessageDto;
use Nelmio\ApiDocBundle\Attribute\Model;
use Nelmio\ApiDocBundle\Attribute\Security;
use OpenApi\Attributes as OA;
use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api')]
final class MessageController extends AbstractController
{
    #[Route('/message', methods: ['POST'], name: 'app_api_message_new')]
    #[OA\Post(
        description: 'Create new message'
    )]
    #[OA\Response(
        response: 400,
        description: 'Return error information of fields missing',
    )]
    #[OA\Response(
        response: 204,
        description: 'Return only 204',
    )]
    #[OA\RequestBody(
        content: new Model(type: MessageDto::class)
    )]
    public function new(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager): JsonResponse
    {
        $data = $request->getContent();
        $message = $serializer->deserialize($data, Message::class, 'json');
        $violations = $validator->validate($message);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = [
                    'field' => $violation->getPropertyPath(),
                    'msg' => $violation->getMessage(),
                ];
            }
            return $this->json([
                'msg' => 'Wrong payload',
                'errors' => $errors
            ], 400);
        }

        $entityManager->persist($message);
        $entityManager->flush();

        return $this->json([], 204);
    }

    #[Route('/message', methods: ['GET'], name: 'app_api_message_list')]
    #[OA\Get(
        description: 'List all messages'
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns list of messages',
    )]
    public function list(EntityManagerInterface $entityManager): JsonResponse
    {
        $messages = $entityManager->getRepository(Message::class)->findAll();
        return $this->json([
            'messages' => $messages,
        ]);
    }
}
