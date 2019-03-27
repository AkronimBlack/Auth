<?php

namespace App\Tests;

use App\Tests\SetUp\DomainTestCase;
use Symfony\Component\HttpFoundation\Response;

class ClientTest extends DomainTestCase
{
    public function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->loadTestDatabase();
    }

    public function testAnewClientCanRegister(): void
    {
        $client = self::createClient();
        $client->request('POST', '/api/register/client' , array(
            'name' => 'test',
            'ip' => 'test'
        ));
        $this->assertSame(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
    }
}