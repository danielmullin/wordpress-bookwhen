<?php

declare (strict_types=1);
namespace InShore\Bookwhen\Vendor\InShore\Bookwhen\Contracts;

use InShore\Bookwhen\Vendor\InShore\Bookwhen\Exceptions\ErrorException;
use InShore\Bookwhen\Vendor\InShore\Bookwhen\Exceptions\TransporterException;
use InShore\Bookwhen\Vendor\InShore\Bookwhen\Exceptions\UnserializableResponse;
use InShore\Bookwhen\Vendor\InShore\Bookwhen\ValueObjects\Transporter\Payload;
use InShore\Bookwhen\Vendor\Psr\Http\Message\ResponseInterface;
/**
 * @internal
 */
interface TransporterContract
{
    /**
     * Sends a request to a server.
     **
     * @return array<array-key, mixed>
     *
     * @throws ErrorException|UnserializableResponse|TransporterException
     */
    public function requestObject(Payload $payload) : array;
}
