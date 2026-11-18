<?php

use Symfony\Component\Dotenv\Dotenv;

@include_once dirname(__DIR__) . '/vendor/autoload.php';

if (!function_exists('load_env')) {
    function load_env()
    {
        static $loaded = false;
        if ($loaded) {
            return;
        }
        $loaded = true;

        $findenv = function ($dir) {
            $result = [];

            if (is_dir($dir)) {
                $dir = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $dir), '/');

                if (is_file($dir . '/.env')) {
                    $result[] = "{$dir}/.env";
                }

                while (dirname($dir) !== $dir) {
                    $dir = dirname($dir);

                    if (is_file($dir . '/.env')) {
                        $result[] = "{$dir}/.env";
                    }
                }
                return !empty($result[0]) ? $result : null;
            }

            return null;
        };

        $locations = array_values(array_unique(array_merge(
            $findenv(dirname(__DIR__)) ?: [],
            $findenv(dirname(getcwd())) ?: []
        )));
        $dot = new Dotenv();
        $_ENV = array_replace([
            // App
            'APP_ENV' => 'dev',
            'APP_DEBUG' => 'false',
            'APP_ID' => 'app',
            'APP_VERSION' => sprintf('%02d.%02d.0', date('y'), date('m')),
            'project.location' => dirname(__DIR__),
        ], $_ENV);

        if (!empty($locations)) {
            $env = array_reverse($locations)[0];
            $app_env_files = ["{$env}.%s", "{$env}.%s.local"];
            $_ENV['project.location'] = dirname($env);

            foreach ($locations as $env) {
                $files = [$env, "{$env}.local"];

                // load .env(.local|)
                foreach ($files as $file) {
                    is_file($file) && $dot->load($file);
                }
            }

            // load .env.(dev|prod)(.local|)
            $app_env = $_ENV['APP_ENV'];

            foreach ($app_env_files as $file) {
                $file = sprintf($file, $app_env);
                is_file($file) && $dot->load($file);
            }
            $_ENV['APP_ENV'] = $app_env;
        }

        foreach (array_keys($_ENV) as $key) {
            if ('null' === $_ENV[$key]) {
                unset($_ENV[$key], $_SERVER[$key]);
            }

            // no float (set semver to %d.%d.%d)
            if ('APP_VERSION' === $key) {
                if (preg_match('#^\d+\.\d+$#', $_ENV[$key])) {
                    $_ENV[$key] .= '.0';
                }
            }
        }

        // initating environment
        env_get('init');

        // debug mode
        @ini_set('display_errors', 0);
        @error_reporting(0);

        if ('true' === $_ENV['APP_DEBUG']) {
            @error_reporting(24575);

            if (constant_get('PHP_VERSION_ID') < 80400) {
                // E_ALL & ~E_DEPRECATED & ~E_STRICT
                @error_reporting(22527);
            }
            @ini_set('display_errors', 1);
        }

        @define('APP_ID', $_ENV['APP_ID']);

        if ('dev' === $_ENV['APP_ENV']) {
            @define('DEV_ENV', true);
        }

        \ApplicationLogger::setLogRoot($_ENV['project.location'] . '/var/log');
        \ApplicationLogger::setRotate(5);
        \ApplicationLogger::setLogDays(true);

        $_SERVER = array_replace([
            'SERVER_NAME' => 'localhost',
            'SERVER_PORT' => 80,
            'HTTP_HOST' => 'localhost',
            'HTTP_USER_AGENT' => 'Symfony',
            'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'HTTP_ACCEPT_LANGUAGE' => 'en-us,en;q=0.5',
            'HTTP_ACCEPT_CHARSET' => 'ISO-8859-1,utf-8;q=0.7,*;q=0.7',
            'REMOTE_ADDR' => '127.0.0.1',
            'SERVER_ADDR' => '127.0.0.1',
            'SCRIPT_NAME' => '',
            'SCRIPT_FILENAME' => '',
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'REQUEST_TIME' => time(),
            'REQUEST_TIME_FLOAT' => microtime(true),
        ], $_SERVER);

        // fix html var dumper on cli env
        if (in_array(php_sapi_name(), ['cli', 'phpdbg', 'embed'])) {
            $_SERVER['VAR_DUMPER_FORMAT'] = 'cli';
            $_SERVER['FORCE_COLOR'] = '1';
            $_ENV['cli.mode'] = 'true';
        }
    }
}