<?php

namespace Tests\Unit;

use App\Services\ResultScoringService;
use Tests\TestCase;

class ResultScoringServiceTest extends TestCase
{
    public function test_it_converts_full_500_raw_marks_to_100_final_marks(): void
    {
        $scores = app(ResultScoringService::class)->calculate(200, 200, 100);

        $this->assertSame(500.0, $scores['total_raw']);
        $this->assertSame(40.0, $scores['exam_final']);
        $this->assertSame(40.0, $scores['lab_final']);
        $this->assertSame(20.0, $scores['classroom_final']);
        $this->assertSame(100.0, $scores['final_score']);
    }

    public function test_it_converts_and_clamps_each_component_safely(): void
    {
        $scores = app(ResultScoringService::class)->calculate(100, 50, 150);

        $this->assertSame(250.0, $scores['total_raw']);
        $this->assertSame(20.0, $scores['exam_final']);
        $this->assertSame(10.0, $scores['lab_final']);
        $this->assertSame(20.0, $scores['classroom_final']);
        $this->assertSame(50.0, $scores['final_score']);
    }
}
