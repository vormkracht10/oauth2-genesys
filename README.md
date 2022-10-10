# Genesys Provider for OAauth 2.0 Client

[![Latest Version on Packagist](https://img.shields.io/packagist/v/vormkracht10/oauth2-genesys.svg?style=flat-square)](https://packagist.org/packages/vormkracht10/oauth2-genesys)
[![Tests](https://github.com/vormkracht10/oauth2-genesys/actions/workflows/run-tests.yml/badge.svg?branch=main)](https://github.com/vormkracht10/oauth2-genesys/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/vormkracht10/oauth2-genesys.svg?style=flat-square)](https://packagist.org/packages/vormkracht10/oauth2-genesys)

This package provides Genesys OAuth 2.0 support for the PHP League's OAuth 2.0 Client.

## Installation

You can install the package via composer:

```bash
composer require vormkracht10/oauth2-genesys
```

## Usage

Usage is the same as The League's OAuth client, using `\Vormkracht10\OAuth2Genesys\Provider\Genesys` as the provider.

### Authorization Code Flow

```php
require_once('./vendor/autoload.php');
session_start();

$provider = new \Vormkracht10\OAuth2Genesys\Provider\Genesys([
    'clientId'          => '{genesys-client-id}',
    'clientSecret'      => '{genesys-client-secret}',
    'redirectUri'       => 'https://example.com/callback-url',
]);

if (!isset($_GET['code'])) {

    // If we don't have an authorization code then get one
    $authUrl = $provider->getAuthorizationUrl();
    $_SESSION['oauth2state'] = $provider->getState();
    header('Location: '.$authUrl);
    exit;

// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {

    unset($_SESSION['oauth2state']);
    exit('Invalid state');

} else {

    // Try to get an access token (using the authorization code grant)
    $token = $provider->getAccessToken('authorization_code', [
        'code' => $_GET['code']
    ]);

    // Optional: Now you have a token you can look up a users profile data
    try {

        // We got an access token, let's now get the user's details
        $user = $provider->getResourceOwner($token);

    } catch (Exception $e) {

        // Failed to get user details
        exit('Oh dear...');
    }

    // Use this to interact with an API on the users behalf
    echo $token->getToken();
}
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

-   [Bas van Dinther](https://github.com/vormkracht10)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
