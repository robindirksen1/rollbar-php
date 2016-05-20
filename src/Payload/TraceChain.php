<?php namespace Rollbar\Payload;

use Rollbar\Payload\Trace;

class TraceChain extends ContentInterface
{
    public function __construct(array $traces)
    {
        $this->setTraces($traces);
    }

    public function getTraces()
    {
        return $this->traces;
    }

    public function setTraces($traces)
    {
        if (count($traces) < 1) {
            throw new \InvalidArgumentException('$traces must contain at least 1 Trace');
        }
        foreach ($traces as $trace) {
            if (!$trace instanceof Trace) {
                throw new \InvalidArgumentException('$traces must all be Trace instances');
            }
        }
        $this->traces = $traces;
        return $this;
    }

    public function jsonSerialize()
    {
        return $this->traces;
    }
}
