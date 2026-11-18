<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use NGSOFT\Cronicle\Client;

$client = Client::fromEnv();

dump('baseUrl', $client->getBaseUrl());

// --- read-only API calls ---

$schedule = $client->getSchedule(0, 5);
dump('getSchedule', $schedule);

$eventId = $schedule->rows[0]->id ?? null;

if ($eventId) {
    dump('getEvent', $client->getEvent($eventId));
    dump('getEventHistory', $client->getEventHistory($eventId, 0, 3));
}

dump('getHistory', $client->getHistory(0, 3));
dump('getActiveJobs', $client->getActiveJobs());
dump('getMasterState', $client->getMasterState());

$activeJobs = $client->getActiveJobs()->jobs ?? [];
$jobId = $activeJobs ? array_key_first($activeJobs) : null;

if (!$jobId) {
    $history = $client->getHistory(0, 1);
    $jobId = $history->rows[0]->id ?? null;
}

if ($jobId) {
    dump('getJobStatus', $client->getJobStatus($jobId));
}

// --- validation errors (expected) ---

try {
    $client->getEvent();
} catch (\InvalidArgumentException $e) {
    dump('getEvent without id/title', $e->getMessage());
}

try {
    $client->runEvent();
} catch (\InvalidArgumentException $e) {
    dump('runEvent without id/title', $e->getMessage());
}

echo "\nOK — client-test completed in " . formated_execution_time() . "\n";
