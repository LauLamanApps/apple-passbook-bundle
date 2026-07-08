<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\Tests\Unit;

use LauLamanApps\ApplePassbookBundle\ApplePassbookBundle;
use LauLamanApps\ApplePassbookBundle\DependencyInjection\ApplePassbookExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ApplePassbookBundle::class)]
class ApplePassbookBundleTest extends TestCase
{
    public function testGetContainerExtension(): void
    {
        $bundle = new ApplePassbookBundle();

        $extension = $bundle->getContainerExtension();

        $this->assertInstanceOf(ApplePassbookExtension::class, $extension);
    }

    public function testGetContainerExtensionTwiceReturnsSameExtension(): void
    {
        $bundle = new ApplePassbookBundle();

        $extension1 = $bundle->getContainerExtension();
        $extension2 = $bundle->getContainerExtension();

        $this->assertInstanceOf(ApplePassbookExtension::class, $extension1);

        $this->assertSame( $extension1, $extension2);
    }
}