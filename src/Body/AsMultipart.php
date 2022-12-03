<?php

declare(strict_types=1);

namespace Jenky\Atlas\Body;

trait AsMultipart
{
    protected $bodyFormat = MultipartPayload::class;
}
