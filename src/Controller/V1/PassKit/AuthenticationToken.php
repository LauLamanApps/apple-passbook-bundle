<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit;

use Symfony\Component\HttpFoundation\Request;

trait AuthenticationToken
{
    protected function getAuthenticationToken(Request $request): string
    {
        return str_replace('ApplePass ', '', $request->headers->get('Authorization', ''));
    }
}
