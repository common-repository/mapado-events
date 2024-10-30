# league-oauth2-provider [![Build Status](https://travis-ci.org/mapado/league-oauth2-provider.svg?branch=master)](https://travis-ci.org/mapado/league-oauth2-provider)

[phpleague OAuth2](http://oauth2-client.thephpleague.com/) provider for Mapado

## Installation

```sh
composer require mapado/league-oauth2-provider
```

## Usage

Usage is the same as The League's OAuth client, using `\Mapado\LeagueOAuth2Provider\Provider\MapadoOAuth2Provider` as the provider.

### Get an client_credentials access token

```php
$provider = new \Mapado\LeagueOAuth2Provider\Provider\MapadoOAuth2Provider([
    'clientId'          => '{mapado-client-id}',
    'clientSecret'      => '{mapado-client-secret}',
]);

$provider->getAccessToken('client_credentials', [
    'scope' => 'scope1 scope2',
]);
```

### Get a password access token

```php
$provider = new \Mapado\LeagueOAuth2Provider\Provider\MapadoOAuth2Provider([
    'clientId'          => '{mapado-client-id}',
    'clientSecret'      => '{mapado-client-secret}',
]);

$provider->getAccessToken('password', [
    'scope' => 'scope1 scope2',
    'username' => 'username',
    'password' => 'password',
]);
```

Both call should return a instance of `League\OAuth2\Client\Token\AccessToken`. See the [phpleague OAuth2 client documentation](http://oauth2-client.thephpleague.com/) for more informations.
