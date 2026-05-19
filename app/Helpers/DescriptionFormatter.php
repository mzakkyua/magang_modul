<?php

namespace App\Helpers;

class DescriptionFormatter
{
    /**
     * Render plain-text description menjadi HTML terstruktur.
     *
     * Pola yang didukung:
     *   "Judul:"          → section header
     *   "- item" / "• item" → bullet list
     *   "1. item"          → numbered list
     *   "> teks"           → quote block
     *   "---" / "___"      → horizontal divider
     *   baris kosong       → jarak antar paragraf
     *   baris biasa        → paragraf
     */
    public static function render(string $raw): string
    {
        $lines = preg_split('/\r\n|\r|\n/', $raw);
        $html  = '';
        $mode  = null; // 'bullet' | 'number' | null

        foreach ($lines as $line) {
            $trimmed = trim($line);

            // Baris kosong
            if ($trimmed === '') {
                $html = self::closeList($html, $mode);
                $mode = null;
                $html .= '<div style="height:8px"></div>';
                continue;
            }

            // Divider (--- atau ___)
            if (preg_match('/^[-_]{3,}$/', $trimmed)) {
                $html = self::closeList($html, $mode);
                $mode = null;
                $html .= '<hr class="desc-divider">';
                continue;
            }

            // Quote block (> teks)
            if (preg_match('/^>\s*(.+)/', $trimmed, $m)) {
                $html = self::closeList($html, $mode);
                $mode = null;
                $html .= '<div class="desc-quote">' . e($m[1]) . '</div>';
                continue;
            }

            // Section header (baris diakhiri : tanpa bullet)
            if (
                preg_match('/^(.{3,60}):$/', $trimmed, $m) &&
                !preg_match('/^[-•*\d]/', $trimmed)
            ) {
                $html = self::closeList($html, $mode);
                $mode = null;
                $html .= '<div class="desc-section-header">'
                       . '<div class="dsh-icon"><i class="bi bi-chevron-right"></i></div>'
                       . e($m[1])
                       . '</div>';
                continue;
            }

            // Numbered list (1. atau 1))
            if (preg_match('/^\d+[.)]\s+(.+)/', $trimmed, $m)) {
                if ($mode !== 'number') {
                    $html = self::closeList($html, $mode);
                    $html .= '<ol class="desc-numlist">';
                    $mode = 'number';
                }
                $html .= '<li>' . e($m[1]) . '</li>';
                continue;
            }

            // Bullet list (-, •, *, ✓, ✗)
            if (preg_match('/^[-•*✓✗]\s+(.+)/', $trimmed, $m)) {
                if ($mode !== 'bullet') {
                    $html = self::closeList($html, $mode);
                    $html .= '<ul class="desc-list">';
                    $mode = 'bullet';
                }
                $html .= '<li>' . e($m[1]) . '</li>';
                continue;
            }

            // Paragraf biasa
            $html = self::closeList($html, $mode);
            $mode = null;
            $html .= '<p class="desc-para">' . e($trimmed) . '</p>';
        }

        // Tutup list yang mungkin masih terbuka di akhir
        $html = self::closeList($html, $mode);

        return $html;
    }

    private static function closeList(string $html, ?string $mode): string
    {
        if ($mode === 'bullet') return $html . '</ul>';
        if ($mode === 'number') return $html . '</ol>';
        return $html;
    }
}