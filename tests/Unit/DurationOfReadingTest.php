<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Helpers\DurationOfReading;

class DurationOfReadingTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testDurationOfReadingTextMethod()
    {

        $text = "this is a test";
        $dor = new DurationOfReading();
        $dor->setText($text);

        $this->assertEquals(4, $dor->getDurationPerSec());
        $this->assertEquals(4 / 60, $dor->getDurationPerMin());
    }
}
