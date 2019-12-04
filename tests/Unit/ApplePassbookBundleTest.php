<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\Tests\Unit;

use LauLamanApps\ApplePassbookBundle\ApplePassbookBundle;
use LauLamanApps\ApplePassbookBundle\DependencyInjection\ApplePassbookExtension;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \LauLamanApps\ApplePassbookBundle\ApplePassbookBundle
 */
class ApplePassbookBundleTest extends TestCase
{
    /**
     * @covers \LauLamanApps\ApplePassbookBundle\ApplePassbookBundle::testGetContainerExtension
     */
    public function testGetContainerExtension(): void
    {
        $bundle = new ApplePassbookBundle();

        $extension = $bundle->getContainerExtension();

        $this->assertInstanceOf(ApplePassbookExtension::class, $extension);
    }

    /**
     * @covers \LauLamanApps\ApplePassbookBundle\ApplePassbookBundle::testGetContainerExtension
     */
    public function testGetContainerExtensionTwiceReturnsSameExtension(): void
    {
        $bundle = new ApplePassbookBundle();

        $extension1 = $bundle->getContainerExtension();
        $extension2 = $bundle->getContainerExtension();

        $this->assertInstanceOf(ApplePassbookExtension::class, $extension1);

        $this->assertSame( $extension1, $extension2);
    }
}