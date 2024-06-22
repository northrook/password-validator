<?php

declare( strict_types = 1 );

namespace Northrook;

use Northrook\PasswordValidator\Result;
use function array_merge;
use function Northrook\Core\Function\numberWithin;

/**
 * A zxcvbn-based password validator.
 *
 * @author  Martin Nielsen <mn@northrook.com>
 *
 * @link    https://github.com/northrook Documentation
 * @todo    Update URL to documentation
 *
 * @link    https://github.com/bjeavons/zxcvbn-php PHP Documentation
 * @link    https://github.com/dropbox/zxcvbn Base Documentation
 */
final class PasswordValidator
{
    /**
     * @param array  $context           Optional context to pass to every validation check.
     * @param int    $requiredStrength  Minimum strength to require for a password.
     */
    public function __construct(
        private readonly array $context = [],
        private readonly int   $requiredStrength = 3,
    ) {}

    /**
     * - Every validation check will the context set in the constructor.
     * - The methods context will be merged with the context passed to the method.
     * - This will not update the context set in the constructor.
     *
     * @param string  $password          Password to validate.
     * @param array   $context           Optional context for this validation.
     * @param ?int    $requiredStrength  Minimum strength to require for a password.
     *
     * @return Result
     */
    public function validate(
        string $password,
        array  $context = [],
        ?int   $requiredStrength = null,
    ) : Result {
        return new Result(
            $password,
            array_values( array_merge( $this->context, $context ) ),
            (int) numberWithin( $requiredStrength ?? $this->requiredStrength, 4, 0 ),
        );
    }
}