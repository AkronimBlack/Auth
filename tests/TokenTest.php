<?php
/**
 * Created by PhpStorm.
 * User: BlackBit
 * Date: 21-Feb-19
 * Time: 19:58
 */

namespace App\Tests;


use App\Tests\SetUp\DomainTestCase;
use Authentication\Resources\DataFixtures\ClientFixture;
use Symfony\Component\HttpFoundation\Response;

class TokenTest extends DomainTestCase
{
    public function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->loadTestDatabase();
    }

    public function testAuthenticatedUserCanGenerateJwtAccessToken(): void
    {
        $client = $this->runAsUserWithBasicAuth();
        $client->request('POST', '/api/user/token/create', array(
            'type'=> 'JWT',
            'intendedFor' => $this->fixtures->getReference(ClientFixture::CLIENT_NAME)->getToken(),
            'subject' => 'test',
            'requestData' => json_encode(['USER_ROLE' , 'USER_USERNAME'])
        ));
        $this->assertSame(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
    }
    public function testAuthenticatedUserCanGenerateBasicAccessToken(): void
    {
        $client = $this->runAsUserWithBasicAuth();
        $client->request('POST', '/api/user/token/create', array(
            'type'=> 'BASIC',
            'intendedFor' => $this->fixtures->getReference(ClientFixture::CLIENT_NAME)->getToken(),
            'subject' => 'test',
            'requestData' => json_encode(['USER_ROLE'])
        ));
        $this->assertSame(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
    }

    public function testUnAuthenticatedUserCanNotGenerateAccessToken(): void
    {
        $client = self::createClient();
        $client->request('POST', '/api/user/token/create', array(
            'type'=> 'JWT',
            'intendedFor' => 'test',
            'subject' => 'test',
            'requestData' => json_encode(['USER_ROLE'])
        ));
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
    }

}
