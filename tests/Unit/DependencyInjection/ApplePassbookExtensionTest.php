<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\Tests\Unit\DependencyInjection;

use LauLamanApps\ApplePassbook\Build\Compiler;
use LauLamanApps\ApplePassbook\Build\Compressor;
use LauLamanApps\ApplePassbook\Build\ManifestGenerator;
use LauLamanApps\ApplePassbook\Build\Signer;
use LauLamanApps\ApplePassbookBundle\DependencyInjection\ApplePassbookExtension;
use LauLamanApps\ApplePassbookBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\Reference;
use ZipArchive;

/**
 * @coversDefaultClass \LauLamanApps\ApplePassbookBundle\DependencyInjection\ApplePassbookExtension
 */
class ApplePassbookExtensionTest extends KernelTestCase
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    public function setUp(): void
    {
        self::bootKernel([
            'environment' => 'test',
            'debug' => false,
        ]);

        $this->container = static::getContainer();

        $config = [
            'certificate' => '<pathToCertificate>',
            'password' => '<certificatePassword>',
            'pass_type_identifier' => '<passTypeIdentifier>',
            'team_identifier' => '<teamIdentifier>',
        ];

        $extension = new ApplePassbookExtension();

        $extension->load([$config], $this->container);
    }

    /**
     * @covers \LauLamanApps\ApplePassbookBundle\DependencyInjection\ApplePassbookExtension::getAlias()
     */
    public function testGetAlias(): void
    {
        $extension = new ApplePassbookExtension();

        $this->assertSame(Configuration::ROOT, $extension->getAlias());
    }

    /**
     * @covers \LauLamanApps\ApplePassbookBundle\DependencyInjection\ApplePassbookExtension::load()
     */
    public function testCompilerIsConfigured(): void
    {
        $expectedArguments = [
            'laulamanapps_apple_passbook.build.manifestgenerator',
            'laulamanapps_apple_passbook.build.signer',
            'laulamanapps_apple_passbook.build.compressor',
            '<passTypeIdentifier>',
            '<teamIdentifier>',
        ];

        $this->assertDefinition(Compiler::class, 'laulamanapps_apple_passbook.build.compiler', $expectedArguments);
        $this->assertAlias(Compiler::class, 'laulamanapps_apple_passbook.build.compiler');
    }

    /**
     * @covers \LauLamanApps\ApplePassbookBundle\DependencyInjection\ApplePassbookExtension::load()
     */
    public function testManifestGeneratorIsConfigured(): void
    {
        $this->assertDefinition(ManifestGenerator::class, 'laulamanapps_apple_passbook.build.manifestgenerator');
    }

    /**
     * @covers \LauLamanApps\ApplePassbookBundle\DependencyInjection\ApplePassbookExtension::load()
     */
    public function testSignerIsConfigured(): void
    {
        $expectedArguments = [
            '<pathToCertificate>',
            '<certificatePassword>',
        ];

        $this->assertDefinition(Signer::class, 'laulamanapps_apple_passbook.build.signer', $expectedArguments);
    }

    /**
     * @covers \LauLamanApps\ApplePassbookBundle\DependencyInjection\ApplePassbookExtension::load()
     */
    public function testZipArchiveIsConfigured(): void
    {
        $this->assertDefinition(ZipArchive::class, 'laulamanapps_apple_passbook.php.zip_archive');
    }

    /**
     * @covers \LauLamanApps\ApplePassbookBundle\DependencyInjection\ApplePassbookExtension::load()
     */
    public function testCompressorIsConfigured(): void
    {
        $expectedArguments = ['laulamanapps_apple_passbook.php.zip_archive',];
        $this->assertDefinition(Compressor::class, 'laulamanapps_apple_passbook.build.compressor', $expectedArguments);
    }


    private function assertDefinition(string $class, string $serviceId, ?array $expectedArguments = null): void
    {
        try {
            $definition = $this->container->getDefinition($serviceId);
        } catch (ServiceNotFoundException $e) {
            $this->fail('Could not load service definition for service id '. $serviceId);

            return;
        }

        $this->assertSame($class, $definition->getClass());

        if ($expectedArguments === null) {
            $this->assertSame([], $definition->getArguments());
        }

        if ($expectedArguments !== null) {
            $this->assertSame(count($expectedArguments), count($definition->getArguments()), 'Number of set arguments does not match the expectation');
            foreach ($definition->getArguments() as $argument) {
                /** @var $argument Reference */

                if (empty($argument)) {
                    continue;
                }

                $this->assertContains((string)$argument, $expectedArguments);
            }
        }
    }

    public function assertAlias(string $aliasId, string $serviceId): void
    {
        try {
            $alias = $this->container->getAlias($aliasId);
        } catch (ServiceNotFoundException $e) {
            $this->fail('Could not load service definition for service id '. $serviceId);

            return;
        }

        $this->assertSame((string)$alias, $serviceId);
    }
}
