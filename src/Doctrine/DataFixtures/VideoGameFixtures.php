<?php

declare(strict_types=1);

namespace App\Doctrine\DataFixtures;

use App\Model\Entity\Tag;
use App\Model\Entity\VideoGame;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

final class VideoGameFixtures extends Fixture implements DependentFixtureInterface
{
    public const VIDEO_GAME_REFERENCE = 'video-game-';
    public const VIDEO_GAME_COUNT = 50;

    public function __construct(
        private readonly Generator $faker,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        /** @var string $fakeText */
        $fakeText = $this->faker->paragraphs(5, true);

        $videoGames = \array_fill_callback(0, self::VIDEO_GAME_COUNT, fn (int $index): VideoGame => (new VideoGame())
            ->setTitle(sprintf('Jeu vidéo %d', $index))
            ->setDescription($fakeText)
            ->setReleaseDate((new \DateTimeImmutable())->sub(new \DateInterval(sprintf('P%dD', $index))))
            ->setTest($fakeText)
            ->setRating(($index % 5) + 1)
            ->setImageName(sprintf('video_game_%d.png', $index))
            ->setImageSize(2_098_872)
        );

        array_walk($videoGames, function (VideoGame $videoGame, int $index): void {
            for ($tagIndex = 0; $tagIndex < 5; ++$tagIndex) {
                /** @var Tag $tag */
                $tag = $this->getReference(
                    TagFixtures::TAG_REFERENCE.(($index + $tagIndex) % TagFixtures::TAG_COUNT),
                    Tag::class
                );
                $videoGame->getTags()->add($tag);
            }
        });

        array_walk($videoGames, [$manager, 'persist']);

        $manager->flush();

        array_walk($videoGames, function (VideoGame $videoGame, int $index): void {
            $this->addReference(self::VIDEO_GAME_REFERENCE.$index, $videoGame);
        });
    }

    public function getDependencies(): array
    {
        return [TagFixtures::class];
    }
}
