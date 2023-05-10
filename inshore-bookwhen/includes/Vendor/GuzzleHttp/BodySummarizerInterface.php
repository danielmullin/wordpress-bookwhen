<?php

namespace InShore\Bookwhen\Vendor\GuzzleHttp;

use InShore\Bookwhen\Vendor\Psr\Http\Message\MessageInterface;
interface BodySummarizerInterface
{
    /**
     * Returns a summarized message body.
     */
    public function summarize(MessageInterface $message) : ?string;
}
