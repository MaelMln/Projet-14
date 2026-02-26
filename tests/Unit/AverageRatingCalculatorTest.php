<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Model\Entity\VideoGame;
use App\Rating\RatingHandler;
use PHPUnit\Framework\TestCase;

final class AverageRatingCalculatorTest extends TestCase
{
    use VideoGameTestTrait;

    /**
     * @dataProvider provideVideoGame
     */
    public function testShouldCalculateAverageRating(VideoGame $videoGame, ?int $expectedAverageRating): void
    {
        $ratingHandler = new RatingHandler();
        $ratingHandler->calculateAverage($videoGame);

        self::assertSame($expectedAverageRating, $videoGame->getAverageRating());
    }

    /**
     * @return iterable<array{VideoGame, ?int}>
     */
    public static function provideVideoGame(): iterable
    {
        yield 'No review' => [new VideoGame(), null];

        yield 'One review' => [self::createVideoGame(5), 5];

        yield 'A lot of reviews' => [
            self::createVideoGame(1, 2, 2, 3, 3, 3, 4, 4, 4, 4, 5, 5, 5, 5, 5),
            4,
        ];
    }
}
