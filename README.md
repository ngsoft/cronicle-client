# cronicle-client

Client PHP pour l’[API JSON REST Cronicle](https://github.com/jhuckaby/Cronicle/blob/master/docs/APIReference.md).

Il encapsule les appels HTTP via `CurlHandler`, hydrate des DTOs typés, et expose un schéma OpenAPI aligné sur ces DTOs.

## Prérequis

- PHP ≥ 8.2
- Composer
- Une instance Cronicle joignable + une clé API

## Installation

```bash
composer install
```

## Configuration

Variables d’environnement (fichier `.env` / `.env.dev`) :

| Variable | Description |
|----------|-------------|
| `CRONICLE_BASE_URL` | URL de base du serveur Cronicle (ex. `https://host:3012`) |
| `CRONICLE_API_KEY` | Clé API (32 caractères hex) |

Exemple :

```env
CRONICLE_BASE_URL=https://cronicle.example.com:3012
CRONICLE_API_KEY=your_api_key_here
```

Le bootstrap charge l’env via `lib/init.php` → `load_env()`.

## Utilisation rapide

```php
require_once __DIR__ . '/lib/init.php';
load_env();

use NGSOFT\Cronicle\Client;
use NGSOFT\Cronicle\Exception\CronicleException;

$client = Client::fromEnv();

try {
    $schedule = $client->getSchedule(0, 50);
    foreach ($schedule->rows as $event) {
        echo $event->id . ' — ' . $event->title . PHP_EOL;
    }

    $state = $client->getMasterState();
    echo 'Scheduler: ' . ($state->state->isEnabled() ? 'on' : 'off') . PHP_EOL;
} catch (CronicleException $e) {
    echo 'API error [' . $e->getApiCode() . ']: ' . $e->getMessage() . PHP_EOL;
}
```

Construction manuelle :

```php
$client = new Client('https://cronicle.example.com:3012', 'your_api_key');
```

## Méthodes du client

| Méthode PHP | Endpoint Cronicle | Réponse DTO |
|-------------|-------------------|-------------|
| `getSchedule(?offset, ?limit)` | `get_schedule` | `ScheduleResponse` |
| `getEvent(?id, ?title)` | `get_event` | `EventResponse` |
| `createEvent(array\|Event)` | `create_event` | `CreateEventResponse` |
| `updateEvent(array\|Event, ?resetCursor, ?abortJobs)` | `update_event` | `ApiResponse` |
| `deleteEvent(id)` | `delete_event` | `ApiResponse` |
| `getEventHistory(id, ?offset, ?limit)` | `get_event_history` | `HistoryResponse` |
| `getHistory(?offset, ?limit)` | `get_history` | `HistoryResponse` |
| `runEvent(?id, ?title, overrides)` | `run_event` | `RunEventResponse` |
| `getJobStatus(id)` | `get_job_status` | `JobStatusResponse` |
| `getActiveJobs()` | `get_active_jobs` | `ActiveJobsResponse` |
| `updateJob(id, fields)` | `update_job` | `ApiResponse` |
| `abortJob(id)` | `abort_job` | `ApiResponse` |
| `getMasterState()` | `get_master_state` | `MasterStateResponse` |
| `updateMasterState(enabled)` | `update_master_state` | `ApiResponse` |

Toutes les URLs sont de la forme `{baseUrl}/api/app/{action}/v1`.  
L’auth est envoyée via le header `X-API-Key`.

En cas d’erreur HTTP ou de `code ≠ 0` dans la réponse JSON, une `CronicleException` est levée.

## DTOs

Namespace `NGSOFT\Cronicle\Dto` :

| Classe | Rôle |
|--------|------|
| `Event` | Objet événement (schedule) |
| `EventTiming` | Timing type cron (`years`, `months`, `days`, …) |
| `Job` | Job actif ou terminé (étend `Event`) |
| `ResourceStats` | Stats CPU / mémoire (`min`, `max`, `total`, `count`, `current`) |
| `ListMeta` | Métadonnées de pagination (`length`, …) |
| `MasterState` | État du scheduler (`enabled`) |

Responses (`NGSOFT\Cronicle\Dto\Response`) :

- `ApiResponse` — enveloppe standard (`code`, `description`)
- `ScheduleResponse`, `EventResponse`, `CreateEventResponse`
- `HistoryResponse`, `RunEventResponse`, `JobStatusResponse`
- `ActiveJobsResponse`, `MasterStateResponse`

Hydratation / sérialisation :

```php
$event = Event::fromArray($payload);
$array = $event->toArray(); // omet les null par défaut
```

## Structure du projet

```
cronicle-client/
├── src/Cronicle/
│   ├── Client.php
│   ├── Exception/CronicleException.php
│   └── Dto/…                  # domain + Response
├── lib/
│   ├── init.php               # bootstrap + dotenv
│   └── libs-no-sql-php82.php  # CurlHandler, helpers
├── examples/
│   └── client-test.php        # smoke test (dump des réponses)
├── openapi.yaml               # schéma OpenAPI 3.1
├── composer.json
└── .env / .env.dev
```

## OpenAPI

Le fichier [`openapi.yaml`](openapi.yaml) décrit les 14 endpoints et les schémas alignés sur les DTOs PHP.

Utile pour Swagger UI, Postman, génération de clients, etc.

Référence officielle : [Cronicle API Reference](https://github.com/jhuckaby/Cronicle/blob/master/docs/APIReference.md).

## Exemple de test

Script de smoke test en lecture seule (affiche les DTOs via `dump()`) :

```bash
php examples/client-test.php
```

Nécessite `CRONICLE_BASE_URL` et `CRONICLE_API_KEY` configurés, et `symfony/var-dumper` (dev).

## Qualité de code

```bash
composer cs:fix
# ou
vendor/bin/php-cs-fixer fix src
```

## Licence / références

- Cronicle : [jhuckaby/Cronicle](https://github.com/jhuckaby/Cronicle)
- API docs : [docs/APIReference.md](https://github.com/jhuckaby/Cronicle/blob/master/docs/APIReference.md)
