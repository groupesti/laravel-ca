<?php

declare(strict_types=1);

namespace CA\Traits;

use Illuminate\Support\Facades\Log;

trait LogsOperations
{
    /**
     * Log a CA operation via CaLog if available, otherwise fallback to Laravel Log.
     *
     * @param  array<string, mixed>  $context
     */
    protected function logOperation(string $operation, string $level, string $message, array $context = []): void
    {
        try {
            if (class_exists(\CA\Log\Facades\CaLog::class)) {
                \CA\Log\Facades\CaLog::log($operation, $level, $message, $context);

                return;
            }
        } catch (\Throwable) {
            // CaLog not available, fallback
        }

        Log::log($level, "[CA:{$operation}] {$message}", $context);
    }

    /**
     * Log an exception via CaLog if available, otherwise fallback to Laravel Log.
     *
     * @param  array<string, mixed>  $context
     */
    protected function logException(\Throwable $e, string $operation, array $context = []): void
    {
        $context = array_merge($context, [
            'operation' => $operation,
            'exception' => $e::class,
            'trace' => $e->getTraceAsString(),
        ]);

        try {
            if (class_exists(\CA\Log\Facades\CaLog::class)) {
                \CA\Log\Facades\CaLog::critical($e->getMessage(), $context);

                return;
            }
        } catch (\Throwable) {
            // CaLog not available, fallback
        }

        Log::critical("[CA:{$operation}] {$e->getMessage()}", $context);
    }
}
