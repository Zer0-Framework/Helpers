<?php

namespace Zer0\Helpers;

/**
 * Class ErrorsAreExceptions
 * @package Zer0\Helpers
 */
class ErrorsAreExceptions
{

    /**
     * @var array
     */
    private static $listeners = [];

    /**
     *
     */
    public static function addListener(callable $cb): void
    {
        self::$listeners[] = $cb;
    }

    /**
     *
     */
    public static function makeItSo(): void
    {
        set_error_handler(function ($severity, $message, $file, $line) {

            $exception = new \ErrorException($message, 0, $severity, $file, $line);
            foreach (self::$listeners as $cb) {
                $cb($exception);
            }

            $errLevel = error_reporting();
            if ($errLevel === 0) {
                return;
            }
            if (!($errLevel & $severity)) {
                return;
            }
            throw $exception;
        });

        assert_options(ASSERT_CALLBACK, function ($script, $line, $message) {
            throw new \AssertionError('Failed assertion ' . json_encode($message,
                    JSON_UNESCAPED_UNICODE) . ' in ' . $script . ':' . $line);
        });
    }
}
