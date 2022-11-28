<?php

declare(strict_types=1);

namespace Jenky\Atlas;

class Payload extends Map
{
    public function __toString()
    {
        return json_encode($this->all());
    }
}
