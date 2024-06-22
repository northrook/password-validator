# Password Validator

A wrapper for the [zxcvbn-php](https://github.com/bjeavons/zxcvbn-php) library.

This package offers a simple class for validating passwords against a zxcvbn-based strength score.

> [!IMPORTANT]
> This package is still in development.
>
> While it is considered MVP and stable, it may still undergo breaking changes.

Slated features:

- [x] Validate passwords using [zxcvbn-php](https://github.com/bjeavons/zxcvbn-php).
- [x] Simple `timeToCrack` method
- [ ] Optional hard limit on passed string, see [issue#74](https://github.com/bjeavons/zxcvbn-php/issues/74#issue-1655751842).
- [ ] Integration with the [UI Component Library](https://github.com/northrook/ui)
- [ ] Optional validation for the `<field:password ... >` component.
- [ ] JavaScript version for real-time validation.
- [ ] Optional validation for the `<field:password ...

## Installation

```bash
composer require northrook/password-validator
```

## Usage

Initialize the `PasswordValidator` class, with an optional global `$context` array.

Use the `validate()` method to validate a given password, returning a `Result` object.

> [!CAUTION]
> The [zxcvbn](https://github.com/dropbox/zxcvbn) library is used under the hood,
> and while it does provide decent insight, it is definite not perfect.
>
> In the example below, we get a score of `3`, despite several matches in the `$context`.

```php
use Northrook\PasswordValidator;

// Optional context for all validations.
$globalContext = [
    'sitename' => 'Example Site',
];   

$validator = new PasswordValidator( $globalContext );

$password = 'example-01-user';
$context  = [
    'username'  => 'Example User',
    'email'     => 'user@example.com',
    'birthdate' => '1980-01-01',
];

$result = $validator->validate( $password, $context ) : Result
```

The `Result` object validates the password using the `zxcvbn-php` library, and sets the following read-only properties:

```php
$pass:bool       // `true` if the password is strong enough, else `false`.
$strength:int    // The strength score of the password.
$label:string    // A human-readable label for the strength score.
$guesses:int     // The number of guesses required to crack the password.
$warning:?string // A warning message if the password is not strong enough, else `null`.
$suggestions:[]  // A list of suggestions to improve the password.
```

The `Result` object also has twh methods:

```php
// Validate the password against a given strength score.
$result->validate( int $strength ):bool

// Get the time to crack the password, in seconds by default.
$time = $result->timeToCrack(
    ?string $scenario = 'online_throttling',  // The zxcvbn-php scenario to use.
     string $return = 'RETURN_SECONDS',       // RETURN_SECONDS, RETURN_LABEL, RETURN_BOTH as object{seconds:int, label:string}.
):int|string|obj

$time->seconds; // 173052000000
$time->label;   // "centuries"
```

## License
[MIT](https://github.com/northrook/password-validator/blob/master/LICENSE)