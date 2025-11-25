<?php
declare(strict_types=1);

namespace GridCP\Common\Infrastructure\Logger;

use Monolog\LogRecord;
use Symfony\Component\HttpFoundation\RequestStack;

final class MonoLogProcessor
{
    public function __construct(private readonly RequestStack $requestStack)
    {

    }

    public function __invoke(LogRecord $record): LogRecord
    {
        $request = $this->requestStack->getMainRequest() ? $this->requestStack->getMainRequest() : null;
        if (!$request) return $record;
        if (!$request->headers->has('X-CID')) return $record;
        $record['extra']['correlation_id'] = $request ? $request->headers->get('X-CID') : null;
        return $record;
    }
}