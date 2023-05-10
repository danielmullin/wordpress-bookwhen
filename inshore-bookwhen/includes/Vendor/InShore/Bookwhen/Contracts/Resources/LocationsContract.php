<?php

namespace InShore\Bookwhen\Vendor\InShore\Bookwhen\Contracts\Resources;

use InShore\Bookwhen\Vendor\InShore\Bookwhen\Responses\Locations\ListResponse;
use InShore\Bookwhen\Vendor\InShore\Bookwhen\Responses\Locations\RetrieveResponse;
interface LocationsContract
{
    /**
     * Returns a list of events that belong to the user's organization.
     *
     * @see https://
     */
    public function list(array $parameters) : ListResponse;
    /**
     * Returns information about a specific event.
     *
     * @see https://
     */
    public function retrieve(string $eventId) : RetrieveResponse;
}
