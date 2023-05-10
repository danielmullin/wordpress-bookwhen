<?php

declare (strict_types=1);
namespace InShore\Bookwhen\Vendor\InShore\Bookwhen\Resources\Concerns;

use InShore\Bookwhen\Vendor\InShore\Bookwhen\Contracts\TransporterContract;
trait Transportable
{
    /**
     * Creates a Client instance with the given API token.
     */
    public function __construct(private readonly TransporterContract $transporter)
    {
        // ..
    }
}
