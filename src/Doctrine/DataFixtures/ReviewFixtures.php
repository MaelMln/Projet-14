<?php

declare(strict_types=1);

namespace App\Doctrine\DataFixtures;

use App\Model\Entity\Review;
use App\Model\Entity\User;
use App\Model\Entity\VideoGame;
use App\Rating\CalculateAverageRating;
use App\Rating\CountRatingsPerValue;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

final class ReviewFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly Generator $faker,
        private readonly CalculateAverageRating $calculateAverageRating,
        private readonly CountRatingsPerValue $countRatingsPerValue
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $users = \array_fill_callback(
            0,
            UserFixtures::USER_COUNT,
            fn (int $index): User => $this->getReference(UserFixtures::USER_REFERENCE.$index, User::class)
        );

        $userChunks = array_chunk($users, 5);

        for ($gameIndex = 0; $gameIndex < VideoGameFixtures::VIDEO_GAME_COUNT; ++$gameIndex) {
            /** @var VideoGame $videoGame */
            $videoGame = $this->getReference(VideoGameFixtures::VIDEO_GAME_REFERENCE.$gameIndex, VideoGame::class);

            $filteredUsers = $userChunks[$gameIndex % 5];

            foreach ($filteredUsers as $user) {
                /** @var string $comment */
                $comment = $this->faker->paragraphs(1, true);

                $review = (new Review())
                    ->setUser($user)
                    ->setVideoGame($videoGame)
                    ->setRating($this->faker->numberBetween(1, 5))
                    ->setComment($comment)
                ;

                $videoGame->getReviews()->add($review);

                $manager->persist($review);
            }

            $this->calculateAverageRating->calculateAverage($videoGame);
            $this->countRatingsPerValue->countRatingsPerValue($videoGame);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [VideoGameFixtures::class, UserFixtures::class];
    }
}
