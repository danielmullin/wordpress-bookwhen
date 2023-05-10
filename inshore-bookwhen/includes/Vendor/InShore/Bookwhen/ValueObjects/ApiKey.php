<?php

declare (strict_types=1);
namespace InShore\Bookwhen\Vendor\InShore\Bookwhen\ValueObjects;

use InShore\Bookwhen\Vendor\InShore\Bookwhen\Contracts\StringableContract;
/**
 * @internal
 */
final class ApiKey implements StringableContract
{
    /**
     * Creates a new API token value object.
     */
    private function __construct(public readonly string $apiKey)
    {
        // ..
    }
    public static function from(string $apiKey) : self
    {
        if (empty($apiKey)) {
            throw new \InvalidArgumentException();
        }
        return new self($apiKey);
    }
    /**
     * {@inheritdoc}
     */
    public function toString() : string
    {
        return $this->apiKey;
    }
}
