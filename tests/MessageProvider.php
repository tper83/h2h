<?php

namespace App\Tests;

use Generator;

trait MessageProvider
{
    public static function messageApiCreateProvider(): Generator
    {
        $payload = [];
        yield 'Case 1 : empty payload' => [
            'payload' => $payload,
            'code' => '404',
            'expected' => ['errors' => 4],
        ];

        $payload['name'] = 'test remove';
        yield 'Case 2 : only name param' => [
            'payload' => $payload,
            'code' => '404',
            'expected' => ['errors' => 3],
        ];

        $payload['email'] = 'test';
        yield 'Case 3 : name and wrong email' => [
            'payload' => $payload,
            'code' => '404',
            'expected' => ['errors' => 3],
        ];

        $payload['email'] = 'test@gmail.com';
        yield 'Case 4 : name, email' => [
            'payload' => $payload,
            'code' => '404',
            'expected' => ['errors' => 2],
        ];

        $payload['content'] = 'test';
        yield 'Case 5 name, email, content' => [
            'payload' => $payload,
            'code' => '404',
            'expected' => ['errors' => 1],
        ];

        $payload['opt_in'] = true;
        yield 'Case 6 all fields provided' => [
            'payload' => $payload,
            'code' => '405',
            'expected' => ['errors' => 0],
        ];
    }


}
