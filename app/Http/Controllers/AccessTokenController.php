<?php

namespace App\Http\Controllers;

use Dusterio\LumenPassport\Http\Controllers\AccessTokenController as LumenPassportAccessTokenController;
use Laravel\Passport\TokenRepository;
use League\OAuth2\Server\AuthorizationServer;
use Lcobucci\JWT\Parser as JwtParser;

/**
 * Passport 11 removed $jwt from the base AccessTokenController; lumen-passport still expects it.
 */
class AccessTokenController extends LumenPassportAccessTokenController
{
    protected JwtParser $jwt;

    public function __construct(AuthorizationServer $server, TokenRepository $tokens, JwtParser $jwt)
    {
        parent::__construct($server, $tokens);
        $this->jwt = $jwt;
    }
}
