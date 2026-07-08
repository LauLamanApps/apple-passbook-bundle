<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\Event;

enum Status: string
{
    case Unhandled = 'UNHANDLED';
    case NotAuthorized = 'NOT_AUTHORIZED';
    case NotModified = 'NOT_MODIFIED';
    case Successful = 'SUCCESSFUL';
    case AlreadyRegistered = 'ALREADY_REGISTERED';
    case NotFound = 'NOT_FOUND';
}
