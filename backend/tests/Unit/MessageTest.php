<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MessageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_required_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('messages', [
            'subject',
            'content',
            'is_read',
            'user_id',
        ]));
    }
}
