<?php

declare(strict_types=1);

namespace App\Enums;

enum PeriodEnum: string
{
    case Hour = 'hour';
    //case Double = '2hours';
    //case Triple = '3hours';
    //case Quadruple = '4hours';
    //case Quintuple = '5hours';
    //case Sextuple = '6hours';
    //case Septuple = '7hours';
    //case Octuple = '8hours';
    //case Nontuple = '9hours';
    //case Dectuple  = '10hours';
    case Day = 'day';

    public function toDateTime(\DateTimeInterface $startTime): \DateTimeImmutable
    {
        $startTime = \DateTimeImmutable::createFromInterface($startTime);

        switch ($this) {
            case self::Hour:
                $interval = \DateInterval::createFromDateString('+1 hour');
                return $startTime->add($interval);
            /*
            case self::Double:
                $interval = \DateInterval::createFromDateString('+2 hour');
                return $startTime->add($interval);
            case self::Triple:
                $interval = \DateInterval::createFromDateString('+3 hour');
                return $startTime->add($interval);
            */
            case self::Day:
                $interval = \DateInterval::createFromDateString('tomorrow');
                return $startTime->add($interval)->setTime(0, 0, 0, 0);
            default:
                $interval = \DateInterval::createFromDateString('+1 hour');
                return $startTime->add($interval);
        }
    }
}
