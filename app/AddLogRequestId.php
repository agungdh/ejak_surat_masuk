<?php

namespace App;

use Illuminate\Support\Facades\Log;

trait AddLogRequestId
{
    public function addRequestId($requestId): void
    {
        Log::getLogger()->pushProcessor(function ($record) use ($requestId) {
            $record['extra']['request_id'] = $requestId;

            return $record;
        });
    }
}
