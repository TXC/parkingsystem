<?php

declare(strict_types=1);

namespace App\Tests;

use TXC\Box\Testing\WithFaker;
use Faker\Factory;

trait WithLicense
{
    protected static function getLicensePlate(string $locale = Factory::DEFAULT_LOCALE): string
    {
        $faker = Factory::create($locale);

        // * - Random number (0-9) / letter (a-z)
        // % - Random number (1-9)
        // # - Random number (0-9)
        // ? - Random letter (a-z)
        $formats = [
            '%??%###',
            '%%??###',
            '???###',
            '??? #??',
            '??? ##?',
            '%???###',
            '???-*##',
            '??-#####',
            '%??-??%',
            '%????%',
            '###-???',
            '### ???',
            '######',
            '??-####',
            '?? ####',
            '####??',
            '???####',
            '??? ##*',
        ];
        return $faker->toUpper($faker->bothify($faker->randomElement($formats)));
    }
}
