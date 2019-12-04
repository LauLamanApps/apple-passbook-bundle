<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\Tests\Unit\Controller\V1\PassKit;

use Symfony\Component\HttpFoundation\Request;

trait RequestHelper
{
    protected function createRequest(string $authenticationToken, ?array $body = null): Request
    {
        $body = $body === null ? []: json_encode($body);

        $request = new Request([], [], [], [], [], [], $body);
        $request->headers->add(['Authorization: ApplePass '. $authenticationToken]);

        return $request;
    }
}