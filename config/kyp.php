<?php

return [
    'scoring' => [
        'exam' => ['raw_max' => 200, 'final_max' => 40],
        'lab' => ['raw_max' => 200, 'final_max' => 40],
        'classroom' => ['raw_max' => 100, 'final_max' => 20],
        'raw_total' => 500,
        'final_total' => 100,
        'pass_mark' => 40,
    ],
    'courseware' => [
        'session_minutes' => 120,
        'heartbeat_seconds' => 15,
        'idle_after_seconds' => 90,
        'passing_score' => 60,
    ],
];
