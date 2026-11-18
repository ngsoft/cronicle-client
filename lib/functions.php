<?php

@define('CRONICLE_APP_START', microtime(true));

require_once __DIR__ . '/libs-no-sql-php82.php';

/**
 * Checks if env is dev.
 *
 * @return bool
 */
function is_dev()
{
    return 'prod' !== env_get('APP_ENV');
}

/**
 * Get script execution time.
 *
 * @param null|float $previous
 * @param int $precision
 *
 * @return float
 */
function execution_time($previous = null, $precision = 6)
{
    if (!isset($previous)) {
        $previous = constant_get('CRONICLE_APP_START');
    }

    return round(microtime(true) - $previous, $precision);
}

/**
 * @param null|float $previous
 * @param int $precision
 *
 * @return string
 */
function formated_execution_time($previous = null, $precision = 6)
{
    $value = execution_time($previous, $precision);

    if ($value < 1.0) {
        return sprintf('%u ms', $value * 1000);
    }

    return sprintf('%.03f sec', $value);
}

/**
 * Replaces backslashes by slashes and removes trailing slashes.
 *
 * @param string $path
 *
 * @return string
 */
function normalize_path($path)
{
    if (empty($path)) {
        return $path;
    }
    return rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $path), '/');
}