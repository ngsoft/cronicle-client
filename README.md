# ngsoft/cronicle-tasks-client

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)
[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D8.2-777BB4.svg)](composer.json)

Client PHP pour l’[API JSON REST Cronicle](https://github.com/jhuckaby/Cronicle/blob/master/docs/APIReference.md).

Il encapsule les appels HTTP via `CurlHandler`, hydrate des DTOs typés, et expose un schéma OpenAPI aligné sur ces DTOs.

**Package Composer :** [`ngsoft/cronicle-tasks-client`](https://packagist.org/packages/ngsoft/cronicle-tasks-client)

## Prérequis

- PHP ≥ 8.2
- Extensions : `curl`, `json`, `mbstring`
- Composer
- Une instance Cronicle joignable + une clé API

## Installation

Via Composer :

```bash
composer require ngsoft/cronicle-tasks-client
```

Développement local (clone du dépôt) :

```bash
composer install
cp .env.example .env
```

## Configuration

Variables d’environnement (fichier `.env` / `.env.dev`, voir `.env.example`) :

| Variable | Description |
|----------|-------------|
| `CRONICLE_BASE_URL` | URL de base du serveur Cronicle (ex. `http://127.0.0.1:3012`) |
| `CRONICLE_API_KEY` | Clé API (32 caractères hex) |

Exemple :

```env
CRONICLE_BASE_URL=https://cronicle.example.com:3012
CRONICLE_API_KEY=your_api_key_here
```

Le bootstrap charge l’env via `lib/init.php` → `load_env()`.  
L’autoload Composer charge déjà `lib/functions.php` (helpers + `CurlHandler`).

## Utilisation rapide

Avec l’autoload Composer (recommandé dans une app) :

```php
require_once __DIR__ . '/vendor/autoload.php';

use NGSOFT\Cronicle\Client;
use NGSOFT\Cronicle\Exception\CronicleException;

// Option A — variables d'environnement déjà chargées
$client = Client::fromEnv();

// Option B — explicite
$client = new Client('https://cronicle.example.com:3012', 'your_api_key');

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

Dans ce dépôt (bootstrap dotenv inclus) :

```php
require_once __DIR__ . '/lib/init.php';
load_env();

$client = Client::fromEnv();
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

## Publication Packagist

1. Pousser le dépôt sur GitHub (`ngsoft/cronicle-client`)
2. Tagger une version semver : `git tag v1.0.0 && git push --tags`
3. Soumettre le dépôt sur [Packagist](https://packagist.org/packages/submit) (ou activer l’auto-update GitHub)
4. Installer ailleurs avec : `composer require ngsoft/cronicle-tasks-client`

## Licence / références

- Licence : [MIT](LICENSE)
- Cronicle : [jhuckaby/Cronicle](https://github.com/jhuckaby/Cronicle)
- API docs : [docs/APIReference.md](https://github.com/jhuckaby/Cronicle/blob/master/docs/APIReference.md)
