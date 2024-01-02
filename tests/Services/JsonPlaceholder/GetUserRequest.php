<?php

declare(strict_types=1);

namespace Fansipan\Tests\Services\JsonPlaceholder;

use Fansipan\Contracts\DecoderInterface;
use Fansipan\Mapper\GenericMapper;
use Fansipan\Request;
use Fansipan\Util;

final class GetUserRequest extends Request
{
    private $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function endpoint(): string
    {
        return 'https://jsonplaceholder.typicode.com/users/'.$this->id;
    }

    public function decoder(): DecoderInterface
    {
        return new GenericMapper(
            static function (iterable $data) {
                return User::fromArray(Util::iteratorToArray($data));
            },
            static function (iterable $data) {
                return User::fromArray(Util::iteratorToArray($data));
            }
        );
    }
}
