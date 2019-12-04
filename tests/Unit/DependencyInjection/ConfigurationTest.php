<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\Tests\Unit\DependencyInjection;

use LauLamanApps\ApplePassbookBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \LauLamanApps\ApplePassbookBundle\DependencyInjection\Configuration
 */
class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    protected function getConfiguration(): Configuration
    {
        return new Configuration();
    }

    /**
     * @covers \LauLamanApps\ApplePassbookBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     */
    public function testRequiredConfiguration(): void
    {
        $this->assertConfigurationIsInvalid(
            [[]],
            'certificate'
        );

        $this->assertConfigurationIsInvalid(
            [['certificate' => '<certificate>']],
            'password'
        );
    }

    /**
     * @covers \LauLamanApps\ApplePassbookBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     */
    public function testDefaultConfiguration(): void
    {
        $this->assertProcessedConfigurationEquals(
            [
                [
                    "certificate" => "<pathToCertificate>",
                    "password" => "<certificatePassword>",
                ]
            ],
            [
                'certificate' => '<pathToCertificate>',
                'password' => '<certificatePassword>',
                'team_identifier' => null,
                'pass_type_identifier' => null,
            ]
        );
    }

    /**
     * @covers \LauLamanApps\ApplePassbookBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     */
    public function testFullConfiguration(): void
    {
        $this->assertProcessedConfigurationEquals(
            [
                [
                    "certificate" => "<pathToCertificate>",
                    "password" => "<certificatePassword>",
                    'team_identifier' => '<TeamId>',
                    'pass_type_identifier' => '<PassTypeId>',
                ]
            ],
            [
                'certificate' => '<pathToCertificate>',
                'password' => '<certificatePassword>',
                'team_identifier' => '<TeamId>',
                'pass_type_identifier' => '<PassTypeId>',
            ]
        );
    }
}