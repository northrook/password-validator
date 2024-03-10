<?php

namespace Northrook\Core;

use ZxcvbnPhp\Zxcvbn;

final class PasswordValidator
{
    private const STRENGTH_MAP = [
        0 => 'UNSAFE',
        1 => 'POOR',
        2 => 'WEAK',
        3 => 'PASSABLE',
        4 => 'STRONG',
    ];

    private readonly object $result;

    public readonly int    $strength;
    public readonly string $label;
    public readonly int    $guesses;
    public readonly ?string $warning;
    public readonly ?array $suggestions;

    public function __construct(
        string $password,
        array  $context = [],
    ) {
        $this->result = (object) ( new Zxcvbn() )->passwordStrength( $password, $context );

        $this->strength = $this->result->score;
        $this->label    = self::STRENGTH_MAP[ $this->strength ];
        $this->guesses  = $this->result->guesses;
        $this->warning  = $this->result->feedback['warning'];
        $this->suggestions = $this->result->feedback['suggestions'];
    }

    public function timeToCrack( ?string $scenario = null ) : object {

        $scenario = match ( $scenario ) {
            'local_fast'    => 'offline_fast_hashing_1e10_per_second',
            'local_slow'    => 'offline_slow_hashing_1e4_per_second',
            'no_throttling' => 'online_no_throttling_10_per_second',
            default         => 'online_throttling_100_per_hour',
        };

        return (object) [
            'seconds'  => $this->result->crack_times_seconds[ $scenario ],
            'label' => $this->result->crack_times_display[ $scenario ],
        ];
    }
}