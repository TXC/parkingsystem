<?php

declare(strict_types=1);

namespace App\Commands;

use App\Domain\Operator;
use App\Domain\Parking;
use App\Domain\Vehicle;
use App\Domain\Ticket;
use App\Domain\Token;
use App\Domain\Zone;
use App\Enums\InfractionEnum;
use App\Enums\PeriodEnum;
use App\Enums\TicketStatusEnum;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TXC\Box\Commands\AbstractCommand;
use TXC\Box\Infrastructure\Environment\Settings;

#[AsCommand(name: 'app:seed', description: 'Seed the application')]
class SeedCommand extends AbstractCommand
{
    private static \Faker\Generator $faker;

    protected function configure(): void
    {
        $this
            //->setAliases(['up-to-date'])
            ->setHelp(<<<EOT
The <info>%command.name%</info> command seeds the database with configured values:

    <info>%command.full_name%</info>
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        self::$faker = \Faker\Factory::create();

        $this->addFakerData();

        return self::SUCCESS;
    }

    private function getPopulator(): \Faker\ORM\Doctrine\Populator
    {
        return new \Faker\ORM\Doctrine\Populator(
            self::$faker,
            $this->getEntityManager()
        );
    }

    private function addFakerData(): array
    {
        $populator = $this->getPopulator();
        $populator->addEntity(Operator::class, 8, [

        ]);
        $populator->execute();

        $populator = $this->getPopulator();
        $populator->addEntity(Token::class, 5, [
            'operator' => function () {
                return $this->getRandomOperator();
            },
            'zone' => function () {
                return $this->getRandomZone();
            },
        ]);
        $populator->addEntity(Vehicle::class, 20, [
            'licensePlate' => function () {
                return $this->licenseGenerator();
            },
        ]);
        $populator->addEntity(Parking::class, 30, [
            'vehicle' => function () {
                return $this->getRandomVehicle();
            },
            'zone' => function () {
                return $this->getRandomZone();
            },
            'startedAt' => function () {
                return $this->getRandomDateTime();
            },
        ]);
        $populator->addEntity(Ticket::class, 15, [
            'zone' => function () {
                return $this->getRandomZone(PeriodEnum::Day);
            },
            'infraction' => function () {
                return $this->getInfraction();
            },
            'status' => function () {
                return $this->getTicketStatus();
            },
            'issuedAt' => function () {
                return $this->getRandomDateTime();
            },
        ]);
        return $populator->execute();
    }

    private function licenseGenerator(): string
    {

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
        return self::$faker->bothify(
            self::$faker->randomElement($formats)
        );
    }

    private function getRandomDateTime(
        ?string $startDate = '-30 years',
        ?string $endDate = '+1 years',
        ?string $timezone = null,
    ): string {
        return self::$faker->dateTimeBetween($startDate, $endDate, $timezone)
                           ->format('Y-m-d H:i:s');
    }

    private function getInfraction(): InfractionEnum
    {
        $values = InfractionEnum::cases();
        shuffle($values);
        return array_pop($values);
    }

    private function getTicketStatus(): TicketStatusEnum
    {
        $values = TicketStatusEnum::cases();
        shuffle($values);
        return array_pop($values);
    }

    private function getPeriod(): PeriodEnum
    {
        $values = PeriodEnum::cases();
        shuffle($values);
        return array_pop($values);
    }

    private function getRandomZone(?PeriodEnum $period = null): Zone
    {
        $zoneNames = ['A', 'B', 'C', 'D'];
        shuffle($zoneNames);

        $period = $period ?? $this->getPeriod();

        $name = array_pop($zoneNames);
        $zone = $this->getEntityManager()->getRepository(Zone::class);
        return $zone->findByNameAndPeriod($name, $period);
    }

    private function getRandomOperator(): Operator
    {
        $repository = $this->getEntityManager()->getRepository(Operator::class);
        $allOperators = $repository->findAll();
        shuffle($allOperators);
        return array_pop($allOperators);
    }


    private function getRandomVehicle(): Vehicle
    {
        $vehicle = $this->getEntityManager()->getRepository(Vehicle::class);
        $allVehicles = $vehicle->findAll();
        shuffle($allVehicles);
        return array_pop($allVehicles);
    }

    private function getVehicle(): Vehicle
    {
        $vehicle = new Vehicle();
        $vehicle->setLicensePlate($this->licenseGenerator());
        return $vehicle;
    }
}
