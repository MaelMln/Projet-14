<?php

declare(strict_types=1);

namespace App\Doctrine\DataFixtures;

use App\Model\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class TagFixtures extends Fixture
{
    public const TAG_REFERENCE = 'tag-';
    public const TAG_COUNT = 25;

    public function load(ObjectManager $manager): void
    {
        $tags = \array_fill_callback(
            0,
            self::TAG_COUNT,
            static fn (int $index): Tag => (new Tag())->setName(sprintf('Tag %d', $index))
        );

        array_walk($tags, [$manager, 'persist']);

        $manager->flush();

        array_walk($tags, function (Tag $tag, int $index): void {
            $this->addReference(self::TAG_REFERENCE.$index, $tag);
        });
    }
}
