<?php

namespace App\Http\Controllers;

use Dusterio\LumenPassport\Http\Controllers\AccessTokenController as LumenPassportAccessTokenController;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Token\Parser as JwtParser;
use Laravel\Passport\TokenRepository;
use League\OAuth2\Server\AuthorizationServer;

/**
 * Passport 11 removed $jwt from the base AccessTokenController; lumen-passport still expects it.
 */
class AccessTokenController extends LumenPassportAccessTokenController
{
    protected JwtParser $jwt;

    public function __construct(AuthorizationServer $server, TokenRepository $tokens)
    {
        parent::__construct($server, $tokens);
        $this->jwt = Configuration::forUnsecuredSigner()->parser();
    }
}
