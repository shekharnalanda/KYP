<?php

namespace App\Services;

class ResultScoringService
{
    public function calculate(float $examRaw, float $labRaw, float $classroomRaw): array
    {
        $examRaw = $this->clamp($examRaw, (float) config('kyp.scoring.exam.raw_max'));
        $labRaw = $this->clamp($labRaw, (float) config('kyp.scoring.lab.raw_max'));
        $classroomRaw = $this->clamp($classroomRaw, (float) config('kyp.scoring.classroom.raw_max'));

        $examFinal = $this->convert($examRaw, 'exam');
        $labFinal = $this->convert($labRaw, 'lab');
        $classroomFinal = $this->convert($classroomRaw, 'classroom');

        return [
            'exam_raw' => $examRaw,
            'lab_raw' => $labRaw,
            'classroom_raw' => $classroomRaw,
            'total_raw' => round($examRaw + $labRaw + $classroomRaw, 2),
            'exam_final' => $examFinal,
            'lab_final' => $labFinal,
            'classroom_final' => $classroomFinal,
            'final_score' => round($examFinal + $labFinal + $classroomFinal, 2),
        ];
    }

    private function convert(float $score, string $component): float
    {
        $rawMax = (float) config("kyp.scoring.{$component}.raw_max");
        $finalMax = (float) config("kyp.scoring.{$component}.final_max");

        return round(($score / $rawMax) * $finalMax, 2);
    }

    private function clamp(float $score, float $maximum): float
    {
        return round(max(0, min($score, $maximum)), 2);
    }
}
