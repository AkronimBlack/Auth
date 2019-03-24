<?php
/**
 * Created by PhpStorm.
 * User: BlackBit
 * Date: 21-Feb-19
 * Time: 19:58
 */

namespace App\Tests;


use App\Tests\SetUp\DomainTestCase;
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
        $client->request('POST', '/api/token/create', array(
            'type'=> 'JWT',
            'intendedFor' => 'test',
            'subject' => 'test',
            'requestData' => json_encode(['USER_ROLE' , 'USER_USERNAME'])
        ));
        $this->assertSame(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
    }
    public function testAuthenticatedUserCanGenerateBasicAccessToken(): void
    {
        $client = $this->runAsUserWithBasicAuth();
        $client->request('POST', '/api/token/create', array(
            'type'=> 'BASIC',
            'intendedFor' => 'test',
            'subject' => 'test',
            'requestData' => json_encode(['USER_ROLE'])
        ));
        $this->assertSame(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
    }

    public function testUnAuthenticatedUserCanNotGenerateAccessToken(): void
    {
        $client = self::createClient();
        $client->request('POST', '/api/token/create', array(
            'type'=> 'JWT',
            'intendedFor' => 'test',
            'subject' => 'test',
            'requestData' => json_encode(['USER_ROLE'])
        ));
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
    }


//    public function testUserCanUseBasicAuthOnlyToCreateToken(): void
//    {
//        $client = $this->runAsAdminWithBasicAuth();
//        $client->request('GET', '/api/token/create', array(
//            'type'=> 'JWT',
//            'intendedFor' => 'test',
//            'subject' => 'test',
//            'requestData' => json_encode(['USER_ROLE'])
//        ));
//        $this->assertSame(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
//
//        $client = $this->runAsAdminWithBasicAuth();
//        $client->request('POST', '/api/role/new', array(
//            'type'=> 'JWT',
//            'intendedFor' => 'test',
//            'subject' => 'test',
//            'requestData' => json_encode(['USER_ROLE'])
//        ));
//        $this->assertSame(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
//    }
}
