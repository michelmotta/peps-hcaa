<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
    #[Test]
    public function it_abbreviates_hours_minutes_and_seconds(): void
    {
        $this->assertEquals('1h 30m 15s', humanAbbreviatedTime('01:30:15'));
    }

    #[Test]
    public function it_abbreviates_only_minutes_and_seconds(): void
    {
        $this->assertEquals('45m 10s', humanAbbreviatedTime('00:45:10'));
    }

    #[Test]
    public function it_abbreviates_only_seconds(): void
    {
        $this->assertEquals('59s', humanAbbreviatedTime('00:00:59'));
    }

    #[Test]
    public function it_abbreviates_only_hours(): void
    {
        $this->assertEquals('2h', humanAbbreviatedTime('02:00:00'));
    }

    #[Test]
    public function it_handles_zero_values_correctly(): void
    {
        $this->assertEquals('1h 5s', humanAbbreviatedTime('01:00:05'));
        $this->assertEquals('1m', humanAbbreviatedTime('00:01:00'));
    }

    #[Test]
    public function it_returns_an_empty_string_for_zero_time(): void
    {
        $this->assertEquals('', humanAbbreviatedTime('00:00:00'));
    }

    #[Test]
    public function it_returns_the_original_string_on_invalid_format(): void
    {
        $this->assertEquals('invalid-time', humanAbbreviatedTime('invalid-time'));
        $this->assertEquals('1:30', humanAbbreviatedTime('1:30'));
    }
}
