<?php

declare(strict_types=1);

namespace Northrook\Core;

use ZxcvbnPhp\Zxcvbn as Validator;

/**
 * A zxcvbn-based password validator.
 *
 * @author  Martin Nielsen <mn@northrook.com>
 *
 * @link    https://github.com/northrook Documentation
 * @todo    Update URL to documentation
 *
 * @link https://github.com/bjeavons/zxcvbn-php PHP Documentation
 * @link https://github.com/dropbox/zxcvbn Base Documentation
 */
final class PasswordValidator
{
    private const STRENGTH_MAP = [
        0 => 'UNSAFE',
        1 => 'POOR',
        2 => 'WEAK',
        3 => 'PASSABLE',
        4 => 'STRONG',
    ];

    private readonly object $validator;

    public readonly int    $strength;
    public readonly string $label;
    public readonly float    $guesses;
    public readonly ?string $warning;
    public readonly ?array $suggestions;

    public function __construct(
        string $password,
        array  $context = [],
    ) {
        $this->validator = (object) ( new Validator() )->passwordStrength( $password, $context );

        $this->strength = $this->validator->score;
        $this->label    = self::STRENGTH_MAP[ $this->strength ];
        $this->guesses  = $this->validator->guesses;
        $this->warning  = $this->validator->feedback[ 'warning'];
        $this->suggestions = $this->validator->feedback[ 'suggestions'];
    }

    public function timeToCrack( ?string $scenario = null ) : object {

        $scenario = match ( $scenario ) {
            'local_fast'    => 'offline_fast_hashing_1e10_per_second',
            'local_slow'    => 'offline_slow_hashing_1e4_per_second',
            'no_throttling' => 'online_no_throttling_10_per_second',
            default         => 'online_throttling_100_per_hour',
        };

        return (object) [
            'seconds'  => $this->validator->crack_times_seconds[ $scenario ],
            'label' => $this->validator->crack_times_display[ $scenario ],
        ];
    }
}