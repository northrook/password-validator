<?php

declare( strict_types = 1 );

namespace Northrook\PasswordValidator;

use ZxcvbnPhp\Zxcvbn;

/**
 * @internal
 *
 * @author Martin Nielsen <mn@northrook.com>
 */
final class Result
{
    private const STRENGTH_MAP = [
        0 => 'UNSAFE',
        1 => 'POOR',
        2 => 'WEAK',
        3 => 'PASSABLE',
        4 => 'STRONG',
    ];

    private readonly array $result;

    public readonly bool    $pass;
    public readonly int     $strength;
    public readonly string  $label;
    public readonly int     $guesses;
    public readonly ?string $warning;
    public readonly ?array  $suggestions;

    public function __construct(
        string $password,
        array  $context,
        int    $requiredStrength,
    ) {
        $this->result = ( new Zxcvbn() )->passwordStrength( $password, $context );

        $this->strength    = $this->result[ 'score' ];
        $this->pass        = $this->validate( $requiredStrength );
        $this->label       = Result::STRENGTH_MAP[ $this->strength ];
        $this->guesses     = (int) $this->result[ 'guesses' ];
        $this->warning     = $this->result[ 'feedback' ][ 'warning' ] ?: null;
        $this->suggestions = $this->result[ 'feedback' ][ 'suggestions' ];
    }

    public function validate( int $strength ) : bool {
        return $this->strength >= $strength;
    }

    /**
     * @param null|string  $scenario  = ['local_fast', 'local_slow', 'online_unthrottled', 'online_throttling'][$any]
     * @param string       $return    = ['RETURN_SECONDS', 'RETURN_LABEL', 'RETURN_BOTH'][$any]
     *
     * @return int|string|object
     */
    public function timeToCrack(
        ?string $scenario = 'online_throttling',
        string  $return = 'RETURN_SECONDS',
    ) : int | string | object {

        $scenario = match ( $scenario ) {
            'local_fast'         => 'offline_fast_hashing_1e10_per_second',
            'local_slow'         => 'offline_slow_hashing_1e4_per_second',
            'online_unthrottled' => 'online_no_throttling_10_per_second',
            default              => 'online_throttling_100_per_hour',
        };

        $seconds = (int) $this->result[ 'crack_times_seconds' ][ $scenario ];
        $label   = (string) $this->result[ 'crack_times_display' ][ $scenario ];

        return match ( $return ) {
            'RETURN_SECONDS' => $seconds,
            'RETURN_LABEL'   => $label,
            default          => (object) [
                'seconds' => $seconds,
                'label'   => $label,
            ],
        };
    }
}