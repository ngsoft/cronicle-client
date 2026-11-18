<?php

namespace NGSOFT\Cronicle;

use NGSOFT\Cronicle\Dto\AbstractDto;
use NGSOFT\Cronicle\Dto\Event;
use NGSOFT\Cronicle\Dto\Response\ActiveJobsResponse;
use NGSOFT\Cronicle\Dto\Response\ApiResponse;
use NGSOFT\Cronicle\Dto\Response\CreateEventResponse;
use NGSOFT\Cronicle\Dto\Response\EventResponse;
use NGSOFT\Cronicle\Dto\Response\HistoryResponse;
use NGSOFT\Cronicle\Dto\Response\JobStatusResponse;
use NGSOFT\Cronicle\Dto\Response\MasterStateResponse;
use NGSOFT\Cronicle\Dto\Response\RunEventResponse;
use NGSOFT\Cronicle\Dto\Response\ScheduleResponse;
use NGSOFT\Cronicle\Exception\CronicleException;

/**
 * HTTP client for the Cronicle JSON REST API.
 *
 * @see https://github.com/jhuckaby/Cronicle/blob/master/docs/APIReference.md
 */
class Client
{
    private string $baseUrl;
    private string $apiKey;
    private int $timeout;

    public function __construct(string $baseUrl, string $apiKey, int $timeout = 30)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->apiKey  = $apiKey;
        $this->timeout = $timeout;
    }

    /**
     * Build a client from CRONICLE_BASE_URL / CRONICLE_API_KEY env vars.
     */
    public static function fromEnv(int $timeout = 30): self
    {
        if ( ! isset($_ENV['SYMFONY_DOTENV_VARS']))
        {
            if ( ! function_exists('load_env'))
            {
                require_once dirname(__DIR__, 2) . '/lib/init.php';
            }
            load_env();
        }
        return new self(
            (string) env_get('CRONICLE_BASE_URL', '', false),
            (string) env_get('CRONICLE_API_KEY', '', false),
            $timeout
        );
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Fetch scheduled events (paginated).
     */
    public function getSchedule(?int $offset = null, ?int $limit = null): ScheduleResponse
    {
        return $this->request(
            'get_schedule',
            $this->filterNull([
                'offset' => $offset,
                'limit'  => $limit,
            ]),
            ScheduleResponse::class
        );
    }

    /**
     * Fetch a single event by ID or exact title.
     */
    public function getEvent(?string $id = null, ?string $title = null): EventResponse
    {
        if (null === $id && null === $title)
        {
            throw new \InvalidArgumentException('Either id or title is required');
        }

        return $this->request(
            'get_event',
            $this->filterNull([
                'id'    => $id,
                'title' => $title,
            ]),
            EventResponse::class
        );
    }

    /**
     * Create a new scheduled event.
     *
     * @param array<string, mixed>|Event $event
     */
    public function createEvent(array|Event $event): CreateEventResponse
    {
        return $this->request(
            'create_event',
            $this->normalizePayload($event),
            CreateEventResponse::class,
            \CurlHandler::METHOD_POST_JSON
        );
    }

    /**
     * Update an existing event.
     *
     * @param array<string, mixed>|Event $event must include id
     */
    public function updateEvent(array|Event $event, ?int $resetCursor = null, ?int $abortJobs = null): ApiResponse
    {
        $payload = $this->normalizePayload($event);

        if (empty($payload['id']))
        {
            throw new \InvalidArgumentException('Event id is required');
        }

        if (null !== $resetCursor)
        {
            $payload['reset_cursor'] = $resetCursor;
        }

        if (null !== $abortJobs)
        {
            $payload['abort_jobs'] = $abortJobs;
        }

        return $this->request(
            'update_event',
            $payload,
            ApiResponse::class,
            \CurlHandler::METHOD_POST_JSON
        );
    }

    /**
     * Delete an event by ID.
     */
    public function deleteEvent(string $id): ApiResponse
    {
        return $this->request(
            'delete_event',
            ['id' => $id],
            ApiResponse::class,
            \CurlHandler::METHOD_POST_JSON
        );
    }

    /**
     * Fetch completed job history for a specific event.
     */
    public function getEventHistory(string $id, ?int $offset = null, ?int $limit = null): HistoryResponse
    {
        return $this->request(
            'get_event_history',
            $this->filterNull([
                'id'     => $id,
                'offset' => $offset,
                'limit'  => $limit,
            ]),
            HistoryResponse::class
        );
    }

    /**
     * Fetch completed job history for all events.
     */
    public function getHistory(?int $offset = null, ?int $limit = null): HistoryResponse
    {
        return $this->request(
            'get_history',
            $this->filterNull([
                'offset' => $offset,
                'limit'  => $limit,
            ]),
            HistoryResponse::class
        );
    }

    /**
     * Run an event on demand.
     *
     * @param array<string, mixed>|Event $overrides optional job overrides (merged into event)
     */
    public function runEvent(?string $id = null, ?string $title = null, array|Event $overrides = []): RunEventResponse
    {
        if (null === $id && null === $title)
        {
            throw new \InvalidArgumentException('Either id or title is required');
        }

        $payload = array_merge(
            $this->normalizePayload($overrides),
            $this->filterNull([
                'id'    => $id,
                'title' => $title,
            ])
        );

        return $this->request(
            'run_event',
            $payload,
            RunEventResponse::class,
            \CurlHandler::METHOD_POST_JSON
        );
    }

    /**
     * Fetch status for a job (active or completed).
     */
    public function getJobStatus(string $id): JobStatusResponse
    {
        return $this->request(
            'get_job_status',
            ['id' => $id],
            JobStatusResponse::class
        );
    }

    /**
     * Fetch all active jobs.
     */
    public function getActiveJobs(): ActiveJobsResponse
    {
        return $this->request('get_active_jobs', [], ActiveJobsResponse::class);
    }

    /**
     * Update a running job (timeout, retries, notifications, resource limits…).
     *
     * @param array<string, mixed> $fields
     */
    public function updateJob(string $id, array $fields): ApiResponse
    {
        $payload       = $fields;
        $payload['id'] = $id;

        return $this->request(
            'update_job',
            $payload,
            ApiResponse::class,
            \CurlHandler::METHOD_POST_JSON
        );
    }

    /**
     * Abort a running job.
     */
    public function abortJob(string $id): ApiResponse
    {
        return $this->request(
            'abort_job',
            ['id' => $id],
            ApiResponse::class,
            \CurlHandler::METHOD_POST_JSON
        );
    }

    /**
     * Fetch master application state (scheduler enabled/disabled).
     */
    public function getMasterState(): MasterStateResponse
    {
        return $this->request('get_master_state', [], MasterStateResponse::class);
    }

    /**
     * Enable or disable the scheduler.
     *
     * @param bool|int $enabled
     */
    public function updateMasterState($enabled): ApiResponse
    {
        return $this->request(
            'update_master_state',
            ['enabled' => (int) (bool) $enabled],
            ApiResponse::class,
            \CurlHandler::METHOD_POST_JSON
        );
    }

    /**
     * @template T of ApiResponse
     *
     * @param array<string, mixed> $params
     * @param class-string<T>      $responseClass
     *
     * @return T
     */
    private function request(
        string $action,
        array $params,
        string $responseClass,
        string $method = \CurlHandler::METHOD_GET
    ): ApiResponse {
        if ('' === $this->baseUrl || '' === $this->apiKey)
        {
            throw new CronicleException('CRONICLE_BASE_URL and CRONICLE_API_KEY must be configured');
        }

        $url      = sprintf('%s/api/app/%s/v1', $this->baseUrl, $action);
        $headers  = [
            'X-API-Key'  => $this->apiKey,
            'Accept'     => 'application/json',
            'User-Agent' => 'cronicle-client/1.0',
        ];

        $response = \CurlHandler::makeHttpRequest(
            $url,
            $params ?: null,
            $method,
            $headers,
            $this->timeout
        );

        if ( ! $response || ! $response->success)
        {
            $status = $response->status ?? 0;
            throw new CronicleException(sprintf(
                'HTTP request to %s failed (HTTP %s)',
                $action,
                $status
            ));
        }

        $decoded  = $response->getDecodedContents();

        if ( ! is_array($decoded))
        {
            throw new CronicleException(sprintf('Invalid JSON response from %s', $action));
        }

        /** @var T $dto */
        $dto      = $responseClass::fromArray($decoded);

        if ( ! $dto->isSuccess())
        {
            throw new CronicleException(
                $dto->description ?? sprintf('Cronicle API error on %s', $action),
                $dto->code
            );
        }

        return $dto;
    }

    /**
     * @param AbstractDto|array<string, mixed> $payload
     *
     * @return array<string, mixed>
     */
    private function normalizePayload(AbstractDto|array $payload): array
    {
        if ($payload instanceof AbstractDto)
        {
            return $payload->toArray();
        }

        return $this->filterNull($payload);
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    private function filterNull(array $data): array
    {
        return array_filter($data, static fn ($value) => null !== $value);
    }
}
