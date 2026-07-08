<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\Tests\Unit\Controller\V1\PassKit;

use Symfony\Component\HttpFoundation\Request;

trait RequestHelper
{
    protected function createRequest(string $authenticationToken, ?array $body = null): Request
    {
        $content = $body === null ? '' : json_encode($body);

        $request = new Request([], [], [], [], [], [], $content);
        $request->headers->set('Authorization', 'ApplePass ' . $authenticationToken);

        return $request;
    }
}
