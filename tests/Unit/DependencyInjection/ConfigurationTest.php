<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\Tests\Unit\DependencyInjection;

use LauLamanApps\ApplePassbookBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Configuration::class)]
class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    protected function getConfiguration(): Configuration
    {
        return new Configuration();
    }

    public function testRequiredConfiguration(): void
    {
        $this->assertConfigurationIsInvalid(
            [[]],
            'certificate'
        );
    }

    public function testDefaultConfiguration(): void
    {
        $this->assertProcessedConfigurationEquals(
            [
                [
                    'certificate' => '<pathToCertificate>',
                ]
            ],
            [
                'certificate' => '<pathToCertificate>',
                'password' => null,
                'team_identifier' => null,
                'pass_type_identifier' => null,
                'environment' => 'production',
            ]
        );
    }

    public function testFullConfiguration(): void
    {
        $this->assertProcessedConfigurationEquals(
            [
                [
                    'certificate' => '<pathToCertificate>',
                    'password' => '<certificatePassword>',
                    'team_identifier' => '<TeamId>',
                    'pass_type_identifier' => '<PassTypeId>',
                    'environment' => 'sandbox',
                ]
            ],
            [
                'certificate' => '<pathToCertificate>',
                'password' => '<certificatePassword>',
                'team_identifier' => '<TeamId>',
                'pass_type_identifier' => '<PassTypeId>',
                'environment' => 'sandbox',
            ]
        );
    }
}
