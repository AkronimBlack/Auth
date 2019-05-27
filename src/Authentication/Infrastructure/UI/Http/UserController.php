<?php
/**
 * Created by PhpStorm.
 * User: BlackBit
 * Date: 15-Dec-18
 * Time: 10:30
 */

namespace Authentication\Infrastructure\UI\Http;


use Authentication\Application\Service\Token\CreateTokenRequest;
use Authentication\Application\Service\Token\CreateTokenService;
use FOS\RestBundle\Controller\Annotations as Rest;
use Authentication\Application\Service\User\AssignRoleToUserRequest;
use Authentication\Application\Service\User\AssignRoleToUserService;
use Authentication\Application\Service\User\CreateUserRequest;
use Authentication\Application\Service\User\CreateUserService;
use Authentication\Application\Service\User\RemoveRoleFromUserRequest;
use Authentication\Application\Service\User\RemoveRoleFromUserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends TransactionalRestController
{
    /**
     * @param CreateUserService $service
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @Rest\Post("/api/user/register" , name="new_user_register")
     */
    public function registerUser(CreateUserService $service, Request $request): JsonResponse
    {
        $response = $this->runAsTransaction(
            $service,
            new CreateUserRequest(
                $request->get('email'),
                $request->get('password'),
                $request->get('username')
            )
        );

        return new JsonResponse($response, Response::HTTP_CREATED);
    }
    /**
     * @param CreateTokenService $service
     *
     * @param Request $request
     *
     *
     * @return JsonResponse
     * @Rest\Post("/api/user/token/create" , name="create_token")
     */
    public function createJwtToken(CreateTokenService $service, Request $request): JsonResponse
    {

        $tokenRequest = new CreateTokenRequest(
            $this->getUser(),
            $request->get('audience'),
            $request->get('type'),
            $request->get('subject'),
            json_decode($request->get('requestData'))
        );

        $response['token'] = $this->runAsTransaction(
            $service,
            $tokenRequest
        );
        return new JsonResponse($response, Response::HTTP_CREATED);
    }

    /**
     * @param AssignRoleToUserService $service
     * @param Request $request
     *
     *
     * @return JsonResponse
     *
     * @Rest\Put("/api/user/role/add" , name="assign_role_to_user")
     */
    public function addRoleToUser(AssignRoleToUserService $service, Request $request): JsonResponse
    {
        //Check that user making the request is higher on the food chain
        $response = $this->runAsTransaction($service, new AssignRoleToUserRequest(
                $request->get('email'),
                $request->get('role'),
                $this->getUser()
            )
        );

        return new JsonResponse($response, Response::HTTP_OK);
    }

    /**
     * @param RemoveRoleFromUserService $service
     * @param Request $request
     *
     *
     * @return JsonResponse
     *
     * @Rest\Put("/api/user/role/remove" , name="remove_role_from_user")
     */
    public function removeRoleFromUser(RemoveRoleFromUserService $service, Request $request): JsonResponse
    {
        $response = $this->runAsTransaction($service,
            new RemoveRoleFromUserRequest(
                $request->get('email'),
                $request->get('role'),
                $this->getUser()
            )
        );

        return new JsonResponse($response, Response::HTTP_OK);
    }
}
