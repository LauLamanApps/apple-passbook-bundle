<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\Event;

use Werkspot\Enum\AbstractEnum;

/**
 * @method static self unhandled()
 * @method bool isUnhandled()
 * @method static self notAuthorized()
 * @method bool isNotAuthorized()
 * @method static self notModified()
 * @method bool isNotModified()
 * @method static self successful()
 * @method bool isSuccessful()
 * @method static self alreadyRegistered()
 * @method bool isAlreadyRegistered()
 * @method static self notFound()
 * @method bool isNotFound()
 */
final class Status extends AbstractEnum
{
    private const UNHANDLED = 'UNHANDLED';
    private const NOT_AUTHORIZED = 'NOT_AUTHORIZED';
    private const NOT_MODIFIED = 'NOT_MODIFIED';
    private const SUCCESSFUL = 'SUCCESSFUL';
    private const ALREADY_REGISTERED = 'ALREADY_REGISTERED';
    private const NOT_FOUND = 'NOT_FOUND';
}
