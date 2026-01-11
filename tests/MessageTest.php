<?php

namespace App\Tests;

use App\Entity\Message;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class MessageTest extends WebTestCase
{
    use MessageProvider;

    private ?EntityManager $entityManager;

    public function testList(): void
    {
        $client = static::createClient();
        $client->request('GET', 'https://127.0.0.1:8000/api/message');
        $response = $client->getResponse();
        $content = json_decode($response->getContent());
        $this->assertResponseIsSuccessful();
        $this->assertCount(10, $content->messages);
    }

    #[DataProvider('messageApiCreateProvider')]
    public function testCreate(array $payload, string $code, array $expected): void
    {
        $client = static::createClient();
        $client->request('POST',
            'https://127.0.0.1:8000/api/message',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload));
        $response = $client->getResponse();
        $code = $response->getStatusCode();
        $content = json_decode($response->getContent());
        $this->assertResponseStatusCodeSame($code, $response->getStatusCode());
        if($code !== 204) {
            $this->assertCount($expected['errors'], $content->errors);
        } else {
            $kernel = self::bootKernel();
            $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
            $repository = $this->entityManager->getRepository(Message::class);
            $message = $repository->findOneBy(['name' => 'test remove']);

            $this->entityManager->remove($message);
            $this->entityManager->flush();
            $this->entityManager->close();
            $this->entityManager = null;
        }
    }

}
