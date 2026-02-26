<?php

declare(strict_types=1);

namespace App\Tests\Functional;

final class SmokeTest extends FunctionalTestCase
{
    /**
     * @dataProvider provideUri
     */
    public function testShouldTestUri(string $method, string $uri, int $expectedStatusCode): void
    {
        $this->client->request($method, $uri);
        self::assertResponseStatusCodeSame($expectedStatusCode);
    }

    /**
     * @return iterable<string, array{string, string, int}>
     */
    public static function provideUri(): iterable
    {
        yield 'GET /' => ['GET', '/', 200];
        yield 'GET /jeu-video-0' => ['GET', '/jeu-video-0', 200];
        yield 'POST /jeu-video-0' => ['POST', '/jeu-video-0', 200];
        yield 'GET /auth/login' => ['GET', '/auth/login', 200];
        yield 'POST /auth/login' => ['POST', '/auth/login', 302];
        yield 'GET /auth/register' => ['GET', '/auth/register', 200];
        yield 'POST /auth/register' => ['POST', '/auth/register', 200];
    }
}
