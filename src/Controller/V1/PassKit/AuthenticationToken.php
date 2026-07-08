<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit;

use Symfony\Component\HttpFoundation\Request;

trait AuthenticationToken
{
    protected function getAuthenticationToken(Request $request): string
    {
        $header = (string) $request->headers->get('Authorization', '');

        if (!str_starts_with($header, 'ApplePass ')) {
            return '';
        }

        return substr($header, strlen('ApplePass '));
    }
}
