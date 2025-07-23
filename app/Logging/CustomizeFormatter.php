<?php

namespace App\Logging;

use Monolog\Formatter\LineFormatter;

class CustomizeFormatter
{
    /**
     * Customize the Monolog instance.
     *
     * @param  \Monolog\Logger  $logger
     * @return void
     */
    public function __invoke($logger)
    {
        foreach ($logger->getHandlers() as $handler) {
            // Ganti format default dengan format kustom
            $formatter = new LineFormatter(
                "[%datetime%] %channel%.%level_name% %extra.request_id%: %message% %context% %extra%\n",
                null,
                true,
                true
            );
            $handler->setFormatter($formatter);
        }
    }
}
