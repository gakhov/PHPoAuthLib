<?php
namespace OAuthTest\Unit\OAuth2\Service;
use OAuth\OAuth2\Service\FitBit;
use OAuth\Common\Token\TokenInterface;
class FitBitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers OAuth\OAuth2\Service\FitBit::__construct
     */
    public function testConstructCorrectInterfaceWithoutCustomUri()
    {
        $service = new FitBit(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );
        $this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\ServiceInterface', $service);
    }
    /**
     * @covers OAuth\OAuth2\Service\FitBit::__construct
     */
    public function testConstructCorrectInstanceWithoutCustomUri()
    {
        $service = new FitBit(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );
        $this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
    }
    /**
     * @covers OAuth\OAuth2\Service\FitBit::__construct
     */
    public function testConstructCorrectInstanceWithCustomUri()
    {
        $service = new FitBit(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            array(),
            $this->getMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );
        $this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
    }
    /**
     * @covers OAuth\OAuth2\Service\FitBit::__construct
     * @covers OAuth\OAuth2\Service\FitBit::getAuthorizationEndpoint
     */
    public function testGetAuthorizationEndpoint()
    {
        $service = new FitBit(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );
        $this->assertSame('https://www.fitbit.com/oauth2/authorize', $service->getAuthorizationEndpoint()->getAbsoluteUri());
    }
    /**
     * @covers OAuth\OAuth2\Service\FitBit::__construct
     * @covers OAuth\OAuth2\Service\FitBit::getAccessTokenEndpoint
     */
    public function testGetAccessTokenEndpoint()
    {
        $service = new FitBit(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );
        $this->assertSame('https://api.fitbit.com/oauth2/token', $service->getAccessTokenEndpoint()->getAbsoluteUri());
    }
    /**
     * @covers OAuth\OAuth2\Service\FitBit::__construct
     * @covers OAuth\OAuth2\Service\FitBit::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseThrowsExceptionOnError()
    {
        $client = $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('error=some_error'));
        $service = new FitBit(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );
        $this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');
        $service->requestAccessToken('foo');
    }
    /**
     * @covers OAuth\OAuth2\Service\FitBit::__construct
     * @covers OAuth\OAuth2\Service\FitBit::parseAccessTokenResponse
     * @covers OAuth\OAuth2\Service\FitBit::requestAccessToken
     */
    public function testParseAccessTokenResponseValid()
    {
        $client = $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('access_token=foo'));
        $service = new FitBit(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );
        $this->assertInstanceOf('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', $service->requestAccessToken('foo'));
    }
}