<?php
namespace App\Support;
/** Display names only; persisted codes, routes and assessment keys stay compatible. */
final class KypModuleNames
{
    public const CODES = ['CIT'=>'CICT','CLS'=>'CLCS','CSS'=>'CPSS'];
    public const NAMES = ['CIT'=>'Certificate in Information and Communication Technology','CLS'=>'Certificate in Language and Communication Skills','CSS'=>'Certificate in Personality and Soft Skills'];
    public static function code(?string $code): string { return self::CODES[$code ?? ''] ?? ($code ?? ''); }
    public static function name(string $code, string $fallback = ''): string {
        $old = array_search($code, self::CODES, true);
        return self::NAMES[$old !== false ? $old : $code] ?? $fallback;
    }
    public static function text(?string $text): string {
        $text = (string)$text;
        $text = preg_replace('/Certificate in Information Technolog(?:y|ies)/i', self::NAMES['CIT'], $text);
        $text = preg_replace('/Certificate in Language Skills?(?! and)/i', self::NAMES['CLS'], $text);
        $text = preg_replace('/Certificate in Soft Skills?/i', self::NAMES['CSS'], $text);
        $text = preg_replace_callback('/\b(CIT|CLS)\b/', fn($m)=>self::CODES[$m[1]], $text);
        return preg_replace('/\bCSS(?=\s*(?:[·—:]|Session|Capstone|Final|module|मॉड्यूल))/u', 'CPSS', $text);
    }
    public static function values(mixed $value): mixed {
        if (is_string($value)) return self::text($value);
        if (is_array($value)) {
            foreach ($value as $key=>$child) {
                if (in_array($key, ['id','code','subject','source_key','source_id','version','url','href','youtube_video_id','theory_youtube_video_id'], true)) continue;
                $value[$key] = self::values($child);
            }
            return $value;
        }
        return $value;
    }
}
