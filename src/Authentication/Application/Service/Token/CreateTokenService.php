<?php
/**
 * Created by PhpStorm.
 * User: BlackBit
 * Date: 18-Feb-19
 * Time: 20:01
 */

namespace Authentication\Application\Service\Token;

use Authentication\Domain\Entity\User\AccessToken;
use Authentication\Domain\Entity\User\User;
use Authentication\Domain\Entity\Values\TokenType;
use Authentication\Domain\Services\Exceptions\TokenTypeNotSupportedException;
use Authentication\Domain\Services\Token\CreateJwtTokenService;
use Firebase\JWT\JWT;
use Transactional\Interfaces\TransactionalServiceInterface;

class CreateTokenService implements TransactionalServiceInterface
{
    /**
     * @var CreateJwtTokenService
     */
    private $createJwtTokenService;

    /**
     * CreateTokenService constructor.
     *
     * @param CreateJwtTokenService $createJwtTokenService
     */
    public function __construct(CreateJwtTokenService $createJwtTokenService)
    {
        $this->createJwtTokenService = $createJwtTokenService;
    }

    /**
     * @param CreateTokenRequest $request
     *
     * @return string
     *
     * @throws \Exception
     */
    public function execute($request = null): string
    {
        $user = $this->setOlderTokensForThatAudienceToInactive($request->getUser(), $request->getIntendedFor());

        switch ($request->getType()){
            case TokenType::JWT_TOKEN:
                $token = $this->createJwtTokenService->execute(
                    $user,
                    $request->getRequestedData(),
                    $request->getIntendedFor(),
                    $request->getSubject()
                );
                break;
            case TokenType::BASIC_TOKEN:
                $token = bin2hex(random_bytes(60));
                break;
            default:
                $this->isPossible($request);
        }

        $user->addAccessToken(
            new AccessToken(
                $request->getType(),
                $request->getIntendedFor(),
                $token
            )
        );
        return $token;
    }

    /**
     * @param CreateTokenRequest $request
     */
    private function isPossible($request): void
    {
        if ( ! in_array($request->getType(), TokenType::getTokenTypes() , false)) {
            throw new TokenTypeNotSupportedException(['type' => $request->getType()]);
        }
    }

    /**
     * @param User $user
     * @param string $audience
     *
     * @return User
     */
    private function setOlderTokensForThatAudienceToInactive(User $user, string $audience): User
    {
        $tokens = $user->getAccessTokens();
        /** @var AccessToken $token */
        foreach ($tokens as $token) {
            if ($token->getAudience() === $audience) {
                $token->setActive(false);
            }
        }

        return $user;
    }
}
