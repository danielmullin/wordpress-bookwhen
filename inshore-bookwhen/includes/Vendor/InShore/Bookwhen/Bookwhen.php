<?php

declare (strict_types=1);
namespace InShore\Bookwhen\Vendor\InShore\Bookwhen;

use InShore\Bookwhen\Vendor\InShore\Bookwhen\BookwhenApi;
use InShore\Bookwhen\Vendor\InShore\Bookwhen\Client;
use InShore\Bookwhen\Vendor\InShore\Bookwhen\Domain\Attachment;
use InShore\Bookwhen\Vendor\InShore\Bookwhen\Domain\ClassPass;
use InShore\Bookwhen\Vendor\InShore\Bookwhen\Domain\Event;
use InShore\Bookwhen\Vendor\InShore\Bookwhen\Domain\Location;
use InShore\Bookwhen\Vendor\InShore\Bookwhen\Domain\Ticket;
use InShore\Bookwhen\Vendor\InShore\Bookwhen\Exceptions\ConfigurationException;
use InShore\Bookwhen\Vendor\InShore\Bookwhen\Exceptions\ValidationException;
use InShore\Bookwhen\Vendor\InShore\Bookwhen\Interfaces\BookwhenInterface;
use InShore\Bookwhen\Vendor\InShore\Bookwhen\Validator;
use InShore\Bookwhen\Vendor\Monolog\Level;
use InShore\Bookwhen\Vendor\Monolog\Logger;
use InShore\Bookwhen\Vendor\Monolog\Handler\StreamHandler;
final class Bookwhen implements BookwhenInterface
{
    /**
     *
     */
    public Attachment $attachment;
    /**
     *
     */
    public array $attachments = [];
    /**
     *
     */
    public Client $client;
    /**
     *
     */
    public ClassPass $classPass;
    /**
     *
     */
    public array $classPasses = [];
    /**
     *
     */
    public Event $event;
    /**
     *
     */
    public array $events = [];
    /**
     *
     */
    private array $filters = [];
    /**
     *
     */
    public Location $location;
    /**
     *
     */
    public array $includes = [];
    /**
     *
     */
    public Ticket $ticket;
    /**
     *
     */
    public array $tickets = [];
    /**
     *
     */
    public $locations = [];
    /** @var string The path to the log file */
    //private $logFile;
    /** @var object loging object. */
    //private $logger;
    /** @var string the logging level. */
    //private string $logLevel;
    /**
     * Creates a new Bookwhen Client with the given API token.
     * @throws ConfigurationException
     * @todo logging
     */
    public function __construct(string $apiKey = null, Client $client = null, private $validator = new Validator())
    {
        //         $this->logFile = $logFile;
        //         $this->logLevel = $logLevel;
        //         $this->logger = new Logger('inShore Bookwhen API');
        //         $this->logger->pushHandler(new StreamHandler($this->logFile, $this->logLevel));
        try {
            if (!\is_null($client)) {
                $this->client = $client;
            } else {
                $this->client = !\is_null($apiKey) ? BookwhenApi::client($apiKey) : (\array_key_exists('INSHORE_BOOKWHEN_API_KEY', $_ENV) ? BookwhenApi::client($_ENV['INSHORE_BOOKWHEN_API_KEY']) : throw new ConfigurationException());
            }
        } catch (\TypeError $e) {
            throw new ConfigurationException();
            // @todo message
        }
    }
    /**
     *
     * @param string $filter
     * @param string $value
     * @param string $validator
     * @throws ValidationException
     */
    public function addFilter(string $filter, null|string $value, string $validator) : void
    {
        if (!\is_null($value) && !$this->validator->{$validator}($value)) {
            throw new ValidationException($filter, $value);
        } else {
            $this->filters['filter[' . $filter . ']'] = $value;
        }
    }
    /**
     *
     * {@inheritDoc}
     * @see \InShore\Bookwhen\Interfaces\ClientInterface::attachment()
     * @todo all attachment properties
     */
    public function attachment(string $attachmentId) : Attachment
    {
        if (!$this->validator->validId($attachmentId, 'attachment')) {
            throw new ValidationException('attachmentId', $attachmentId);
        }
        $attachment = $this->client->attachments()->retrieve($attachmentId);
        return $this->attachment = new Attachment($attachment->contentType, $attachment->fileUrl, $attachment->fileSizeBytes, $attachment->fileSizeText, $attachment->fileName, $attachment->fileType, $attachment->id, $attachment->title);
    }
    /**
     *
     * {@inheritDoc}
     *
     * @see \InShore\Bookwhen\Interfaces\BookwhenInterface::attachment()
     */
    public function attachments(string $title = null, string $fileName = null, string $fileType = null) : array
    {
        $this->addFilter('title', $title, 'validTitle');
        $this->addFilter('file_name', $fileName, 'validFileName');
        $this->addFilter('file_type', $fileType, 'validFileType');
        $attachments = $this->client->attachments()->list($this->filters);
        foreach ($attachments->data as $attachment) {
            \array_push($this->attachments, new Attachment($attachment->contentType, $attachment->fileUrl, $attachment->fileSizeBytes, $attachment->fileSizeText, $attachment->fileName, $attachment->fileType, $attachment->id, $attachment->title));
        }
        return $this->attachments;
    }
    /**
     *
     * {@inheritDoc}
     * @see \InShore\Bookwhen\Interfaces\ClientInterface::getClassPass()
     * @todo
     */
    public function classPass(string $classPassId) : ClassPass
    {
        if (!$this->validator->validId($classPassId, 'classPass')) {
            throw new ValidationException('classPassId', $classPassId);
        }
        $classPass = $this->client->classPasses()->retrieve($classPassId);
        return new ClassPass($classPass->details, $classPass->id, $classPass->numberAvailable, $classPass->title, $classPass->usageAllowance, $classPass->usageType, $classPass->useRestrictedForDays);
    }
    /**
     *
     * {@inheritDoc}
     * @see \InShore\Bookwhen\Interfaces\ClientInterface::getClassPasses()
     */
    public function classPasses($cost = null, $detail = null, $title = null, $usageAllowance = null, $usageType = null, $useRestrictedForDays = null) : array
    {
        $this->addFilter('detail', $detail, 'validDetails');
        $this->addFilter('title', $title, 'validTitle');
        $this->addFilter('usage_allowance', $usageAllowance, 'validUsageAllowance');
        $this->addFilter('usage_type', $usageType, 'validUsageType');
        $this->addFilter('use_restricted_for_days', $useRestrictedForDays, 'validUseRestrictedForDays');
        $classPasses = $this->client->classPasses()->list($this->filters);
        foreach ($classPasses->data as $classPass) {
            \array_push($this->classPasses, new ClassPass($classPass->details, $classPass->id, $classPass->numberAvailable, $classPass->title, $classPass->usageAllowance, $classPass->usageType, $classPass->useRestrictedForDays));
        }
        return $this->classPasses;
    }
    /**
     *
     * {@inheritDoc}
     * @see \InShore\Bookwhen\Interfaces\BookwhenInterface::event()
     */
    public function event(string $eventId, bool $includeAttachments = \false, bool $includeLocation = \false, bool $includeTickets = \false, bool $includeTicketsClassPasses = \false, bool $includeTicketsEvents = \false) : Event
    {
        if (!$this->validator->validId($eventId, 'event')) {
            throw new ValidationException('eventId', $eventId);
        }
        // Validate $includeAttachments;
        if (!$this->validator->validInclude($includeAttachments)) {
            throw new ValidationException('includeAttachments', $includeAttachments);
        }
        // Validate $includeTickets;
        if (!$this->validator->validInclude($includeLocation)) {
            throw new ValidationException('includeLocation', $includeLocation);
        }
        // Validate $includeTickets;
        if (!$this->validator->validInclude($includeTickets)) {
            throw new ValidationException('includeTickets', $includeTickets);
        }
        // Validate $includeTicketsEvents;
        if (!$this->validator->validInclude($includeTicketsEvents)) {
            throw new ValidationException('includeTicketsEvents', $includeTicketsEvents);
        }
        // Validate $includeTicketsClassPasses;
        if (!$this->validator->validInclude($includeTicketsClassPasses)) {
            throw new ValidationException('includeTicketsClassPasses', $includeTicketsClassPasses);
        }
        $includesMapping = ['attachments' => $includeAttachments, 'location' => $includeLocation, 'tickets' => $includeTickets, 'tickets.events' => $includeTicketsEvents, 'tickets.class_passes' => $includeTicketsClassPasses];
        $this->includes = \array_keys(\array_filter($includesMapping, function ($value) {
            return $value;
        }));
        $event = $this->client->events()->retrieve($eventId, ['include' => \implode(',', $this->includes)]);
        // attachments
        $eventAttachments = [];
        foreach ($event->attachments as $eventAttachment) {
            $attachment = $this->client->attachments()->retrieve($eventAttachment->id);
            \array_push($eventAttachments, new Attachment($attachment->contentType, $attachment->fileUrl, $attachment->fileSizeBytes, $attachment->fileSizeText, $attachment->fileName, $attachment->fileType, $attachment->id, $attachment->title));
        }
        // eventTickets
        $eventTickets = [];
        foreach ($event->tickets as $ticket) {
            \array_push($eventTickets, new Ticket($ticket->available, $ticket->availableFrom, $ticket->availableTo, $ticket->builtBasketUrl, $ticket->builtBasketIframeUrl, $ticket->cost, $ticket->courseTicket, $ticket->details, $ticket->groupTicket, $ticket->groupMin, $ticket->groupMax, $ticket->id, $ticket->numberIssued, $ticket->numberTaken, $ticket->title));
        }
        // ticketsClassPasses
        // @todo
        return $this->event = new Event($event->allDay, $eventAttachments, $event->attendeeCount, $event->attendeeLimit, $event->details, $event->endAt, $event->id, new Location($event->location->additionalInfo, $event->location->addressText, $event->location->id, $event->location->latitude, $event->location->longitude, $event->location->mapUrl, $event->location->zoom), $event->maxTicketsPerBooking, $event->startAt, $eventTickets, $event->title, $event->waitingList);
    }
    /**
     *
     * {@inheritDoc}
     * @see \InShore\Bookwhen\Interfaces\ClientInterface::getEvents()
     */
    public function events($calendar = \false, $entry = \false, $location = [], $tags = [], $title = [], $detail = [], $from = null, $to = null, bool $includeAttachments = \false, bool $includeLocation = \false, bool $includeTickets = \false, bool $includeTicketsClassPasses = \false, bool $includeTicketsEvents = \false) : array
    {
        //$this->logger->debug(__METHOD__ . '(' . var_export(func_get_args(), true) . ')');
        // Validate $calendar
        // @todo details required
        // Validate $detail
        if (!empty($detail)) {
            if (!\is_array($detail)) {
                throw new ValidationException('detail', \implode(' ', $detail));
            } else {
                $detail = \array_unique($detail);
                foreach ($detail as $item) {
                    if (!$this->validator->validLocation($item)) {
                        throw new ValidationException('detail', $item);
                    }
                }
                $this->filters['filter[detail]'] = \implode(',', $detail);
            }
        }
        // Validate $entry
        // @todo details required
        // Validate $from;
        if (!empty($from)) {
            if (!$this->validator->validFrom($from, $to)) {
                throw new ValidationException('from', $from . '-' . $to);
            } else {
                $this->filters['filter[from]'] = $from;
            }
        }
        // Validate $location
        if (!empty($location)) {
            if (!\is_array($location)) {
                throw new ValidationException('location', \implode(' ', $title));
            } else {
                $location = \array_unique($location);
                foreach ($location as $item) {
                    if (!$this->validator->validLocation($item)) {
                        throw new ValidationException('location', $item);
                    }
                }
                $this->filters['filter[location]'] = \implode(',', $location);
            }
        }
        // Validate $tags.
        if (!empty($tags)) {
            if (!\is_array($tags)) {
                throw new ValidationException('tags', \implode(' ', $tags));
            } else {
                $tags = \array_unique($tags);
                foreach ($tags as $tag) {
                    if (!empty($tag) && !$this->validator->validTag($tag)) {
                        throw new ValidationException('tag', $tag);
                    }
                }
            }
            $this->filters['filter[tag]'] = \implode(',', $tags);
        }
        // Validate $title;
        if (!empty($title)) {
            if (!\is_array($title)) {
                throw new ValidationException('title', \implode(' ', $title));
            } else {
                $title = \array_unique($title);
                foreach ($title as $item) {
                    if (!$this->validator->validTitle($item)) {
                        throw new ValidationException('title', $item);
                    }
                }
                $this->filters['filter[title]'] = \implode(',', $title);
            }
        }
        // Validate $to;
        if (!empty($to)) {
            if (!$this->validator->validTo($to, $from)) {
                throw new ValidationException('to', $to . '-' . $from);
            } else {
                $this->filters['filter[to]'] = $to;
            }
        }
        // Validate $includeTickets;
        if (!$this->validator->validInclude($includeLocation)) {
            throw new ValidationException('includeLocation', $includeLocation);
        }
        // Validate $includeTickets;
        if (!$this->validator->validInclude($includeTickets)) {
            throw new ValidationException('includeTickets', $includeTickets);
        }
        // Validate $includeTicketsEvents;
        if (!$this->validator->validInclude($includeTicketsEvents)) {
            throw new ValidationException('includeTicketsEvents', $includeTicketsEvents);
        }
        // Validate $includeTicketsClassPasses;
        if (!$this->validator->validInclude($includeTicketsClassPasses)) {
            throw new ValidationException('includeTicketsClassPasses', $includeTicketsClassPasses);
        }
        $includesMapping = ['location' => $includeLocation, 'tickets' => $includeTickets, 'tickets.events' => $includeTicketsEvents, 'tickets.class_passes' => $includeTicketsClassPasses];
        $this->includes = \array_keys(\array_filter($includesMapping, function ($value) {
            return $value;
        }));
        $events = $this->client->events()->list(\array_merge($this->filters, ['include' => \implode(',', $this->includes)]));
        foreach ($events->data as $event) {
            $eventTickets = [];
            foreach ($event->tickets as $ticket) {
                \array_push($eventTickets, new Ticket($ticket->available, $ticket->availableFrom, $ticket->availableTo, $ticket->builtBasketUrl, $ticket->builtBasketIframeUrl, $ticket->cost, $ticket->courseTicket, $ticket->details, $ticket->groupTicket, $ticket->groupMin, $ticket->groupMax, $ticket->id, $ticket->numberIssued, $ticket->numberTaken, $ticket->title));
            }
            \array_push($this->events, new Event($event->allDay, $event->attachments, $event->attendeeCount, $event->attendeeLimit, $event->details, $event->endAt, $event->id, new Location($event->location->additionalInfo, $event->location->addressText, $event->location->id, $event->location->latitude, $event->location->longitude, $event->location->mapUrl, $event->location->zoom), $event->maxTicketsPerBooking, $event->startAt, $eventTickets, $event->title, $event->waitingList));
        }
        return $this->events;
    }
    /*
     *
     * {@inheritDoc}
     * @see \InShore\Bookwhen\Interfaces\ClientInterface::getLocation()
     */
    public function location(string $locationId) : Location
    {
        if (!$this->validator->validId($locationId, 'location')) {
            throw new ValidationException('locationId', $locationId);
        }
        $location = $this->client->locations()->retrieve($locationId);
        return $this->location = new Location($location->additionalInfo, $location->addressText, $location->id, $location->latitude, $location->longitude, $location->mapUrl, $location->zoom);
    }
    /**
     *
     */
    public function locations(null|string $addressText = null, null|string $additionalInfo = null) : array
    {
        $this->addFilter('additional_info', $additionalInfo, 'validAdditionalInfo');
        $this->addFilter('address_text', $addressText, 'validAddressText');
        $locations = $this->client->locations()->list($this->filters);
        foreach ($locations->data as $location) {
            \array_push($this->locations, new Location($location->additionalInfo, $location->addressText, $location->id, $location->latitude, $location->longitude, $location->mapUrl, $location->zoom));
        }
        return $this->locations;
    }
    //     /**
    //      * Set Debug.
    //      * @deprecated
    //      */
    //     public function setLogging($level)
    //     {
    //         $this->logLevel = $level;
    //     }
    /**
     * {@inheritDoc}
     * @see \InShore\Bookwhen\Interfaces\BookWhenInterface::ticket()
     * class_passes
     */
    public function ticket(string $ticketId, bool $includeClassPasses = \false, bool $includeEvents = \false, bool $includeEventsAttachments = \false, bool $includeEventsLocation = \false, bool $includeEventsTickets = \false) : Ticket
    {
        // ticketId
        if (!$this->validator->validId($ticketId, 'ticket')) {
            throw new ValidationException('ticketId', $ticketId);
        }
        // Validate $includeClassPasses;
        if (!$this->validator->validInclude($includeClassPasses)) {
            throw new ValidationException('includeClassPasses', $includeClassPasses);
        }
        // Validate $includeEvents;
        if (!$this->validator->validInclude($includeEvents)) {
            throw new ValidationException('includeEvents', $includeEvents);
        }
        // Validate $includeAttachments;
        if (!$this->validator->validInclude($includeEventsAttachments)) {
            throw new ValidationException('includeEventssAttachments', $includeEventsAttachments);
        }
        // Validate $includeEventsLocation;
        if (!$this->validator->validInclude($includeEventsLocation)) {
            throw new ValidationException('includeEventsLocation', $includeEventsLocation);
        }
        // Validate $includeEventsTickets;
        if (!$this->validator->validInclude($includeEventsTickets)) {
            throw new ValidationException('includeEventsTickets', $includeEventsTickets);
        }
        $includesMapping = ['class_passes' => $includeClassPasses, 'events' => $includeEvents, 'events.attachments' => $includeEventsAttachments, 'events.location' => $includeEventsLocation, 'events.tickets' => $includeEventsTickets];
        $this->includes = \array_keys(\array_filter($includesMapping, function ($value) {
            return $value;
        }));
        $ticket = $this->client->tickets()->retrieve($ticketId);
        return $this->ticket = new Ticket($ticket->available, $ticket->availableFrom, $ticket->availableTo, $ticket->builtBasketUrl, $ticket->builtBasketIframeUrl, $ticket->cost, $ticket->courseTicket, $ticket->details, $ticket->groupTicket, $ticket->groupMin, $ticket->groupMax, $ticket->id, $ticket->numberIssued, $ticket->numberTaken, $ticket->title);
    }
    /**
     * {@inheritDoc}
     * @see \InShore\Bookwhen\Interfaces\BookWhenInterface::tickets()
     * @todo includes
     */
    public function tickets(string $eventId, bool $includeClassPasses = \false, bool $includeEvents = \false, bool $includeEventsAttachments = \false, bool $includeEventsLocation = \false, bool $includeEventsTickets = \false) : array
    {
        // $this->logger->debug(__METHOD__ . '(' . var_export(func_get_args(), true) . ')');
        if (!$this->validator->validId($eventId, 'event')) {
            throw new ValidationException('eventId', $eventId);
        }
        // Validate $includeClassPasses;
        if (!$this->validator->validInclude($includeClassPasses)) {
            throw new ValidationException('includeClassPasses', $includeClassPasses);
        }
        // Validate $includeEvents;
        if (!$this->validator->validInclude($includeEvents)) {
            throw new ValidationException('includeEvents', $includeEvents);
        }
        // Validate $includeAttachments;
        if (!$this->validator->validInclude($includeEventsAttachments)) {
            throw new ValidationException('includeEventssAttachments', $includeEventsAttachments);
        }
        // Validate $includeEventsLocation;
        if (!$this->validator->validInclude($includeEventsLocation)) {
            throw new ValidationException('includeEventsLocation', $includeEventsLocation);
        }
        // Validate $includeEventsTickets;
        if (!$this->validator->validInclude($includeEventsTickets)) {
            throw new ValidationException('includeEventsTickets', $includeEventsTickets);
        }
        $includesMapping = ['class_passes' => $includeClassPasses, 'events' => $includeEvents, 'events.attachments' => $includeEventsAttachments, 'events.location' => $includeEventsLocation, 'events.tickets' => $includeEventsTickets];
        $this->includes = \array_keys(\array_filter($includesMapping, function ($value) {
            return $value;
        }));
        $tickets = $this->client->tickets()->list(\array_merge(['event_id' => $eventId], ['include' => \implode(',', $this->includes)]));
        foreach ($tickets->data as $ticket) {
            \array_push($this->tickets, new Ticket($ticket->available, $ticket->availableFrom, $ticket->availableTo, $ticket->builtBasketUrl, $ticket->builtBasketIframeUrl, $ticket->cost, $ticket->courseTicket, $ticket->details, $ticket->groupTicket, $ticket->groupMin, $ticket->groupMax, $ticket->id, $ticket->numberIssued, $ticket->numberTaken, $ticket->title));
        }
        return $this->tickets;
    }
}
