<?php

namespace InShore\Bookwhen\Vendor\Http\Discovery\Strategy;

use InShore\Bookwhen\Vendor\Http\Client\HttpAsyncClient;
use InShore\Bookwhen\Vendor\Http\Client\HttpClient;
use InShore\Bookwhen\Vendor\Http\Mock\Client as Mock;
/**
 * Find the Mock client.
 *
 * @author Sam Rapaport <me@samrapdev.com>
 */
final class MockClientStrategy implements DiscoveryStrategy
{
    /**
     * {@inheritdoc}
     */
    public static function getCandidates($type)
    {
        if (\is_a(HttpClient::class, $type, \true) || \is_a(HttpAsyncClient::class, $type, \true)) {
            return [['class' => Mock::class, 'condition' => Mock::class]];
        }
        return [];
    }
}
