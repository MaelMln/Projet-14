<?php

declare(strict_types=1);

namespace App\Doctrine\DataFixtures;

use App\Model\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class UserFixtures extends Fixture
{
    public const USER_REFERENCE = 'user-';
    public const USER_COUNT = 25;

    public function load(ObjectManager $manager): void
    {
        $users = \array_fill_callback(0, self::USER_COUNT, fn (int $index): User => (new User())
            ->setEmail(sprintf('user+%d@email.com', $index))
            ->setPlainPassword('password')
            ->setUsername(sprintf('user+%d', $index))
        );

        array_walk($users, [$manager, 'persist']);

        $manager->flush();

        array_walk($users, function (User $user, int $index): void {
            $this->addReference(self::USER_REFERENCE.$index, $user);
        });
    }
}
