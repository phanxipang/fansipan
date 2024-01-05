<?php

declare(strict_types=1);

namespace Fansipan\Tests\Services\JsonPlaceholder;

final class User
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $phone;

    /**
     * @var string
     */
    public $website;

    public static function fromArray(array $data): self
    {
        $self = new self();

        $self->id = $data['id'] ?? 0;
        $self->name = $data['name'] ?? '';
        $self->email = $data['email'] ?? '';
        $self->phone = $data['phone'] ?? '';
        $self->website = $data['website'] ?? '';

        return $self;
    }
}
