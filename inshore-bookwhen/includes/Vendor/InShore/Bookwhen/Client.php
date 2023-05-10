<?php

declare (strict_types=1);
namespace InShore\Bookwhen\Vendor\InShore\Bookwhen;

use InShore\Bookwhen\Vendor\GuzzleHttp;
use InShore\Bookwhen\Vendor\GuzzleHttp\Client as GuzzleClient;
use InShore\Bookwhen\Vendor\GuzzleHttp\Psr7\Request;
use InShore\Bookwhen\Vendor\InShore\Bookwhen\Exceptions\ConfigurationException;
use InShore\Bookwhen\Vendor\InShore\Bookwhen\Exceptions\RestException;
use InShore\Bookwhen\Vendor\InShore\Bookwhen\Exceptions\ValidationException;
use InShore\Bookwhen\Vendor\InShore\Bookwhen\Interfaces\ClientInterface;
use InShore\Bookwhen\Vendor\InShore\Bookwhen\Validator;
use InShore\Bookwhen\Vendor\Monolog\Level;
use InShore\Bookwhen\Vendor\Monolog\Logger;
use InShore\Bookwhen\Vendor\Monolog\Handler\StreamHandler;
use InShore\Bookwhen\Vendor\Psr\Http\Message\ResponseInterface;
use InShore\Bookwhen\Vendor\InShore\Bookwhen\Resources\Attachments;
use InShore\Bookwhen\Vendor\InShore\Bookwhen\Resources\ClassPasses;
use InShore\Bookwhen\Vendor\InShore\Bookwhen\Resources\Events;
use InShore\Bookwhen\Vendor\InShore\Bookwhen\Resources\Locations;
use InShore\Bookwhen\Vendor\InShore\Bookwhen\Resources\Tickets;
/**
 * Class Client
 *
 * The main class for API consumption
 *
 * @package inshore\bookwhen
 * @todo comments
 * @todo externalise config
 * @todo fix token
 */
class Client implements ClientInterface
{
    /**
     * {@inheritDoc}
     * @see \InShore\Bookwhen\Interfaces\ClientInterface::__construct()
     * @todo sanity check the log level passed in an exception if wrong.
     * @todo handle guzzle error
     */
    public function __construct(private $transporter)
    {
    }
    /**
     *
     */
    public function attachments() : Attachments
    {
        return new Attachments($this->transporter);
    }
    /**
     */
    public function classPasses() : ClassPasses
    {
        return new ClassPasses($this->transporter);
    }
    /*
     *
     */
    public function events() : Events
    {
        return new Events($this->transporter);
    }
    /**
     */
    public function locations() : Locations
    {
        return new Locations($this->transporter);
    }
    /**
     */
    public function tickets() : Tickets
    {
        return new Tickets($this->transporter);
    }
}
// EOF!
