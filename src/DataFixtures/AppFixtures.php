<?php

namespace App\DataFixtures;

use App\Entity\Message;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // create 10 messages
        for ($i = 0; $i < 10; $i++) {
            $message = new Message();
            $message->setName('person '.$i);
            $message->setContent('test');
            $message->setEmail("person$i@gmail.com");
            $message->setOptIn(true);
            $manager->persist($message);
        }

        $manager->flush();
    }
}
