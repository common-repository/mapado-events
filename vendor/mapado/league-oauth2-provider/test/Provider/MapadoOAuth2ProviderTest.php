<?php

namespace Mapado\LeagueOAuth2Provider\Tests\Units\Provider;

use GuzzleHttp\ClientInterface;
use Mapado\LeagueOAuth2Provider\Provider\MapadoOAuth2Provider;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Http\Message\ResponseInterface;
/**
 * Class MapadoOAuthProvider
 * @author Julien Deniau <julien.deniau@mapado.com>
 */
class MapadoOAuth2ProviderTest extends TestCase
{
    protected $provider;
    protected function setUp()
    {
        $this->provider = new MapadoOAuth2Provider(['clientId' => 'mock_client_id', 'clientSecret' => 'mock_secret']);
    }
    public function testAuthorizationUrl()
    {
        $url = $this->provider->getAuthorizationUrl();
        $uri = parse_url($url);
        parse_str($uri['query'], $query);
        $this->assertArrayHasKey('client_id', $query);
        $this->assertArrayHasKey('state', $query);
        $this->assertArrayHasKey('scope', $query);
        $this->assertArrayHasKey('response_type', $query);
        $this->assertArrayHasKey('approval_prompt', $query);
        $this->assertNotNull($this->provider->getState());
    }
    public function testGetAuthorizationUrl()
    {
        $url = $this->provider->getAuthorizationUrl();
        $uri = parse_url($url);
        $this->assertEmpty(isset($uri['path']) ? $uri['path'] : null);
    }
    public function testGetBaseAccessTokenUrl()
    {
        $params = [];
        $url = $this->provider->getBaseAccessTokenUrl($params);
        $uri = parse_url($url);
        $this->assertEquals('/oauth/v2/token', $uri['path']);
    }
    public function testGetAccessToken()
    {
        $response = $this->prophesize(ResponseInterface::class);
        $response->getBody()->shouldBeCalled()->willReturn(json_encode(['access_token' => 'mock_access_token', 'expires_in' => 3600, 'scope' => 'scope1 scope2', 'token_type' => 'bearer', 'refresh_token' => 'mock_refresh_token']));
        $response->getHeader('content-type')->shouldBeCalled()->willReturn(['content-type' => 'json']);
        $response->getStatusCode()->shouldBeCalled()->willReturn(200);
        $client = $this->prophesize(ClientInterface::class);
        $client->send(Argument::any())->shouldBeCalled()->willReturn($response->reveal());
        $this->provider->setHttpClient($client->reveal());
        $accessToken = $this->provider->getAccessToken('client_credentials');
        $this->assertEquals('mock_access_token', $accessToken->getToken());
        $this->assertEquals('mock_refresh_token', $accessToken->getRefreshToken());
        $this->assertEquals('bearer', $accessToken->getValues()['token_type']);
        $this->assertGreaterThan(time() + 3000, $accessToken->getExpires());
        $this->assertFalse($accessToken->hasExpired());
    }
    /**
     * @expectedException League\OAuth2\Client\Provider\Exception\IdentityProviderException
     * @expectedExceptionMessage Error spotted
     **/
    public function testExceptionOnAccessToken()
    {
        $response = $this->prophesize(ResponseInterface::class);
        $response->getBody()->shouldBeCalled()->willReturn(json_encode(['error_description' => 'Error spotted']));
        $response->getHeader('content-type')->shouldBeCalled()->willReturn(['content-type' => 'json']);
        $response->getStatusCode()->shouldBeCalled()->willReturn(mt_rand(400, 600));
        $client = $this->prophesize(ClientInterface::class);
        $client->send(Argument::any())->shouldBeCalled()->willReturn($response->reveal());
        $this->provider->setHttpClient($client->reveal());
        $accessToken = $this->provider->getAccessToken('client_credentials');
    }
}