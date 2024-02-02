<?php

use Kiwilan\Notifier\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

/**
 * @return array{
 *  NOTIFIER_DISCORD_WEBHOOK: string,
 *  NOTIFIER_SLACK_WEBHOOK: string,
 *  NOTIFIER_MAIL_MAILER: string,
 *  NOTIFIER_MAIL_HOST: string,
 *  NOTIFIER_MAIL_PORT: string,
 *  NOTIFIER_MAIL_USERNAME: string,
 *  NOTIFIER_MAIL_PASSWORD: string,
 *  NOTIFIER_MAIL_ENCRYPTION: string,
 *  NOTIFIER_MAIL_FROM_ADDRESS: string,
 *  NOTIFIER_MAIL_FROM_NAME: string,
 * }
 */
function dotenv(): array
{
    $path = __DIR__.'/../';
    $lines = file($path.'.env');
    $dotenv = [];

    foreach ($lines as $line) {
        if (! empty($line)) {
            $data = explode('=', $line);
            $key = $data[0];
            if ($key === " \n ") {
                continue;
            }
            $value = $data[1] ?? null;

            $key = trim($key);
            $value = trim($value);

            if ($key === '') {
                continue;
            }

            $value = str_replace('"', '', $value);
            $value = str_replace("'", '', $value);

            $dotenv[$key] = $value;
        }
    }

    return $dotenv;
}

function getLog(): string
{
    $os = PHP_OS;
    $cmd = match ($os) {
        'Windows' => 'php --info | findstr /r /c:"error_log"',
        default => 'php --info | grep error',
    };

    // dump($cmd);
    $output = exec($cmd);
    $log_path_regex = '/error_log => (.*)/';
    preg_match($log_path_regex, $output, $matches);
    dump($matches);
    // dump($output);

    return $matches[1];
}
