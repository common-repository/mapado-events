PHP SDK for Mapado API [![Build Status](https://travis-ci.org/mapado/php-sdk.svg?branch=master)](https://travis-ci.org/mapado/php-sdk) [![StyleCI](https://styleci.io/repos/28038267/shield)](https://styleci.io/repos/28038267)
=======

⚠ Work in progress ⚠

This SDK targets version 2 of mapado api.
If you are looking for the version 1, you can refer to [the old documentation](https://github.com/mapado/php-sdk/tree/api-v1)

## Installation
Install with [composer](http://getcomposer.org):
```
composer require "mapado/php-sdk"
```

This SDK leverages the power of our [rest-client-sdk](https://github.com/mapado/rest-client-sdk).

## Usage

### Get a token

### Refresh a token

### Create a client

### Activities
```php
use Mapado\Sdk\Entity\Activity;

$activities = $client->getRepository(Activity::class)->findBy(['q' => 'transbordeur', latlng => '45.468,4.263']);
$activity = $client->getRepository(Activity::class)->findOne('63e4b024-6f08-43d3-a73b-634a306bc6c6');
```
Will return an MapadoList or an instance of [Activity](https://github.com/mapado/php-sdk/blob/master/src/Model/Activity.php)

#### Program
If an activity contains a program, you can get it by doing this call
```php
$activityList = $client->getRepository(Activity::class)->findProgramFor('villeurbanne--transbordeur');
```
Will return an MapadoList or an instance of [Activity](https://github.com/mapado/php-sdk/blob/master/src/Model/Activity.php)

### Users
```php
use Mapado\Sdk\Entity\User;

$me = $client->getRepository(User::class)->me();
$user = $client->getRepository(User::class)->findOne('63e4b024-6f08-43d3-a73b-634a306bc6c6');
```
Will return an instance of [User](https://github.com/mapado/php-sdk/blob/master/src/Model/User.php)

#### User Lists

### List objects
Lists results will be contained in a [MapadoList](https://github.com/mapado/php-sdk/blob/master/src/Model/MapadoList.php) object.
The list object is traversable (you can iterate on it with a foreach),
and may contains some informations like totalhits, page, etc.


### Other calls
Every endpoint listed in our documentation *should* be available like the one atop.
If a endpoint is missing, please open an issue on github
