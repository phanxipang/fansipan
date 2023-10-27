<?php

declare(strict_types=1);

namespace Fansipan\Tests;

use Fansipan\Body\RawPayload;
use Fansipan\Map;

final class PayloadTest extends TestCase
{
    public function test_map(): void
    {
        $map = new Map(['foo' => 'bar'], 'foo');

        $map->with('name', 'John')
            ->remove('foo');

        $this->assertSame('John', $map['name']);
        $this->assertArrayNotHasKey('foo', $map);

        $this->expectException(\UnexpectedValueException::class);
        $map->set('hello world');

        $map->push(['age' => 20, 'email' => 'john@example.com']);

        $this->assertCount(3, $map);

        unset($map['age']);
        $this->assertFalse($map->has('age'));

        $map['age'] = 30;
        $this->assertSame(30, $map['age']);
    }

    public function test_raw_payload(): void
    {
        $raw = new RawPayload();

        $this->assertTrue($raw->isEmpty());

        $raw->set('hello world');

        $this->assertSame('hello world', $raw->all());

        $raw->push('!');

        $this->assertSame('hello world!', (string) $raw);

        $this->expectException(\LogicException::class);
        $raw->with('foo', 'bar');

        $this->expectException(\LogicException::class);
        $raw->merge('abc');

        $this->expectException(\LogicException::class);
        $raw->remove('foo');
    }
}
