<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle;

use LauLamanApps\ApplePassbookBundle\DependencyInjection\ApplePassbookExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class ApplePassbookBundle extends Bundle
{
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new ApplePassbookExtension();
        }
        return $this->extension;
    }
}
