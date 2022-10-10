<?php

namespace Vormkracht10\OAuth2Genesys\Provider;

use League\OAuth2\Client\Grant\AbstractGrant;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;
use Vormkracht10\OAuth2Genesys\Token\GenesysAccessToken;

class Genesys extends AbstractProvider
{
    use BearerAuthorizationTrait;

    public const ACCESS_TOKEN_RESOURCE_OWNER_ID = 'info.id';

    public string $apiDomain = 'https://api.mypurecloud.com/api/v2';

    public string $authDomain = 'https://login.mypurecloud.com';

    public function __construct(array $options = [])
    {
        parent::__construct($options);

        if (isset($options['region'])) {
            $this->setRegion($options['region']);
        }
    }

    /**
     * Set the correct API and Auth domains based on the region
     * @see https://developer.genesys.cloud/platform/api/
     *
     * @param string $region
     * @return void
     */
    private function setRegion(string $region): void
    {
        switch ($region) {
            case 'us-east-1':
                $this->apiDomain = 'https://api.mypurecloud.com/api/v2';
                $this->authDomain = 'https://login.mypurecloud.com';

                break;
            case 'us-east-2':
                $this->apiDomain = 'https://api.use2.us-gov-pure.cloud/api/v2';
                $this->authDomain = 'https://login.use2.us-gov-pure.cloud';

                break;
            case 'us-west-2':
                $this->apiDomain = 'https://api.usw2.pure.cloud/api/v2';
                $this->authDomain = 'https://login.usw2.pure.cloud';

                break;
            case 'ca-central-1':
                $this->apiDomain = 'https://api.cac1.pure.cloud/api/v2';
                $this->authDomain = 'https://login.cac1.pure.cloud';

                break;
            case 'sa-east-1':
                $this->apiDomain = 'https://api.sae1.pure.cloud/api/v2';
                $this->authDomain = 'https://login.sae1.pure.cloud';

                break;
            case 'eu-central-1':
                $this->apiDomain = 'https://api.mypurecloud.de/api/v2';
                $this->authDomain = 'https://login.mypurecloud.de';

                break;
            case 'eu-west-1':
                $this->apiDomain = 'https://api.mypurecloud.ie/api/v2';
                $this->authDomain = 'https://login.mypurecloud.ie';

                break;
            case 'eu-west-2':
                $this->apiDomain = 'https://api.euw2.pure.cloud/api/v2';
                $this->authDomain = 'https://login.euw2.pure.cloud';

                break;
            case 'ap-south-1':
                $this->apiDomain = 'https://api.aps1.pure.cloud/api/v2';
                $this->authDomain = 'https://login.aps1.pure.cloud';

                break;
            case 'ap-northeast-2':
                $this->apiDomain = 'https://api.apne2.pure.cloud/api/v2';
                $this->authDomain = 'https://login.apne2.pure.cloud';

                break;
            case 'ap-southeast-2':
                $this->apiDomain = 'https://api.mypurecloud.com.au/api/v2';
                $this->authDomain = 'https://login.mypurecloud.com.au';

                break;
            case 'ap-northeast-1':
                $this->apiDomain = 'https://api.mypurecloud.jp/api/v2';
                $this->authDomain = 'https://login.mypurecloud.jp';

                break;
        }
    }

    /**
     * Get authorization url to begin OAuth flow
     *
     * @return string
     */
    public function getBaseAuthorizationUrl(): string
    {
        return $this->authDomain . '/oauth/authorize';
    }

    /**
     * Get access token url to retrieve token
     *
     * @param array $params
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params): string
    {
        return $this->authDomain . '/oauth/token';
    }

    /**
     * Get provider url to fetch user details
     *
     * @param AccessToken $token
     *
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return $this->apiDomain . '/users/me?' . http_build_query([
            'access_token' => $token->getToken(),
        ]);
    }

    /**
     * Get the default scopes used by this provider.
     *
     * This should not be a complete list of all scopes, but the minimum
     * required for the provider user interface!
     *
     * @return array
     */
    protected function getDefaultScopes(): array
    {
        return [];
    }

    /**
     * Check a provider response for errors.
     *
     * @throws IdentityProviderException
     * @param  ResponseInterface $response
     * @param  array|string $data Parsed response data
     * @return void
     */
    protected function checkResponse(ResponseInterface $response, $data): void
    {
        if ($response->getStatusCode() >= 400) {
            throw new IdentityProviderException(
                $data['error'] ?? $response->getReasonPhrase(),
                $response->getStatusCode(),
                $response
            );
        }
    }

    /**
     * Generate a user object from a successful user details request.
     *
     * @param array $response
     * @param AccessToken $token
     *
     * @return ResourceOwnerInterface
     */
    protected function createResourceOwner(array $response, AccessToken $token): ResourceOwnerInterface
    {
        return new GenesysResourceOwner($response);
    }

    /**
     * Returns the authorization headers used by this provider.
     *
     * @param  array $response
     * @param AbstractGrant $grant
     * @return AccessTokenInterface
     */
    protected function createAccessToken(array $response, AbstractGrant $grant): AccessTokenInterface
    {
        return new GenesysAccessToken($response);
    }
}
