<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\Tests\Unit\Controller\V1\PassKit;

use LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\AuthenticationToken;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

#[CoversTrait(AuthenticationToken::class)]
class AuthenticationTokenTest extends TestCase
{
    #[DataProvider('authorizationHeaderProvider')]
    public function testGetAuthenticationToken(?string $header, string $expectedToken): void
    {
        $request = new Request();
        if ($header !== null) {
            $request->headers->set('Authorization', $header);
        }

        self::assertSame($expectedToken, $this->getExtractor()->extract($request));
    }

    /**
     * @return array<string, array{string|null, string}>
     */
    public static function authorizationHeaderProvider(): array
    {
        return [
            'valid header' => ['ApplePass secret-token', 'secret-token'],
            'missing header' => [null, ''],
            'empty header' => ['', ''],
            'wrong scheme' => ['Bearer secret-token', ''],
            'bare token without scheme' => ['secret-token', ''],
            'scheme substring in the middle is not stripped' => ['ApplePass abcApplePass def', 'abcApplePass def'],
            'lowercase scheme is rejected' => ['applepass secret-token', ''],
        ];
    }

    private function getExtractor(): object
    {
        return new class {
            use AuthenticationToken;

            public function extract(Request $request): string
            {
                return $this->getAuthenticationToken($request);
            }
        };
    }
}
