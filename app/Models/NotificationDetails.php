<?php

namespace App\Models;

use Carbon\Carbon;

class NotificationDetails
{
    private const LABEL = '[\p{Lu}][\p{L}\s\/()–-]{0,40}';

    /**
     * Tách nội dung thông báo dạng "Nhãn: giá trị" thành các dòng của bảng thông tin.
     *
     * @return array{summary: list<string>, rows: list<array{label: string, value: string}>}
     */
    public function parse(string $message): array
    {
        $summary = [];
        $rows = [];

        foreach ($this->segments($message) as $segment) {
            if (preg_match('/^('.self::LABEL.'):\s+(.+)$/su', $segment, $match)) {
                $rows[] = ['label' => trim($match[1]), 'value' => $this->formatValue($match[2])];
            } elseif ($rows === []) {
                $summary[] = $segment;
            } elseif (end($rows)['label'] === 'Khóa') {
                $rows[] = ['label' => 'Bài học', 'value' => $this->formatValue($segment)];
            } else {
                $rows[array_key_last($rows)]['value'] .= "\n".$this->formatValue($segment);
            }
        }

        return ['summary' => $summary, 'rows' => $rows];
    }

    /**
     * @return list<string>
     */
    private function segments(string $message): array
    {
        $segments = [];

        foreach (preg_split('/\R/u', $message) as $line) {
            foreach (preg_split('/(?<=\.)\s+(?='.self::LABEL.':\s)/u', trim($line)) as $part) {
                if (trim($part) !== '') {
                    $segments[] = trim($part);
                }
            }
        }

        return $segments;
    }

    private function formatValue(string $value): string
    {
        $value = rtrim(trim($value), '.');

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return Carbon::parse($value)->format('d/m/Y');
        }

        return $value;
    }
}
