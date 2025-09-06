<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class SimpleCalculatorTest extends TestCase
{
    /**
     * Test sederhana untuk membuktikan testing infrastructure bekerja
     */
    public function test_simple_addition()
    {
        $result = 2 + 3;
        $this->assertEquals(5, $result);
    }

    /**
     * Test untuk membuktikan PHP unit test berfungsi
     */
    public function test_simple_string_concatenation()
    {
        $greeting = 'Hello' . ' ' . 'World';
        $this->assertEquals('Hello World', $greeting);
    }

    /**
     * Test untuk membuktikan array handling
     */
    public function test_array_manipulation()
    {
        $data = ['medical', 'system', 'webkhanza'];
        $this->assertCount(3, $data);
        $this->assertContains('medical', $data);
        $this->assertContains('webkhanza', $data);
    }
}