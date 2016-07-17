<?php
namespace OAuth\OAuth2\Service;

use OAuth\OAuth2\Token\StdOAuth2Token;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\Uri\Uri;
use OAuth\Common\Consumer\CredentialsInterface;
use OAuth\Common\Http\Client\ClientInterface;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Http\Uri\UriInterface;

/**
 * FitBit service.
 *
 * @author Andrii Gakhov <andrii.gakhov@gmail.com>
 * @link https://dev.fitbit.com/docs/oauth2/
 */
class FitBit extends AbstractService
{
    /**
     * Defined scopes
     *
     *
     * @link https://dev.fitbit.com/docs/oauth2/#scope
     */
    const SCOPE_ACTIVITY   = 'activity';
    const SCOPE_HEARTRATE  = 'heartrate';
    const SCOPE_LOCATION   = 'location';
    const SCOPE_NUTRITION  = 'nutrition';
    const SCOPE_PROFILE    = 'profile';
    const SCOPE_SETTINGS   = 'settings';
    const SCOPE_SLEEP      = 'sleep';
    const SCOPE_SOCIAL     = 'social';
    const SCOPE_WEIGHT     = 'weight';


    public function __construct(
        CredentialsInterface $credentials,
        ClientInterface $httpClient,
        TokenStorageInterface $storage,
        $scopes = array(),
        UriInterface $baseApiUri = null
    ) {
        parent::__construct($credentials, $httpClient, $storage, $scopes, $baseApiUri, true);
        if (null === $baseApiUri) {
            $this->baseApiUri = new Uri('https://api.fitbit.com/1/');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorizationUri(array $additionalParameters = array())
    {
        $parameters = array_merge(
            $additionalParameters,
            array(
                'client_id'     => $this->credentials->getConsumerId(),
                'redirect_uri'  => $this->credentials->getCallbackUrl(),
                'response_type' => 'code',
            )
        );

        $parameters['scope'] = implode(' ', $this->scopes);

        // Build the url
        $url = clone $this->getAuthorizationEndpoint();
        foreach ($parameters as $key => $val) {
            $url->addToQuery($key, $val);
        }

        return $url;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorizationEndpoint()
    {
        return new Uri('https://www.fitbit.com/oauth2/authorize');
    }
    /**
     * {@inheritdoc}
     */
    public function getAccessTokenEndpoint()
    {
        return new Uri('https://api.fitbit.com/oauth2/token');
    }
    /**
     * {@inheritdoc}
     */
    protected function getAuthorizationMethod()
    {
        return static::AUTHORIZATION_METHOD_HEADER_BEARER;
    }

    /**
     * {@inheritdoc}
     */
    protected function parseAccessTokenResponse($responseBody)
    {
        $data = json_decode($responseBody, true);

        if (null === $data || !is_array($data)) {
            throw new TokenResponseException('Unable to parse response.');
        } elseif (isset($data['error'])) {
            throw new TokenResponseException('Error in retrieving token: "' . $data['error'] . '"');
        }

        $token = new StdOAuth2Token();
        $token->setAccessToken($data['access_token']);
        $token->setLifeTime($data['expires_in']);

        if (isset($data['refresh_token'])) {
            $token->setRefreshToken($data['refresh_token']);
            unset($data['refresh_token']);
        }

        unset($data['access_token']);
        unset($data['expires_in']);

        $token->setExtraParams($data);

        return $token;
    }
}