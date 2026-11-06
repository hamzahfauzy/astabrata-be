<?php

namespace Libraries\Logging;

interface LogDriverInterface
{
    public function write(array $data): bool;
}