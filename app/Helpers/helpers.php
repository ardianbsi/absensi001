<?php

use Carbon\Carbon;

if (!function_exists('toast_success')) {
    function toast_success($message): void
    {
        session()->flash('toast_success', $message);
    }
}

if (!function_exists('toast_error')) {
    function toast_error($message): void
    {
        session()->flash('toast_error', $message);
    }
}

if (!function_exists('format_date')) {
    function format_date($date, string $format = 'd M Y'): string
    {
        return $date ? Carbon::parse($date)->format($format) : '-';
    }
}

if (!function_exists('format_datetime')) {
    function format_datetime($datetime): string
    {
        return $datetime ? Carbon::parse($datetime)->format('d M Y H:i') : '-';
    }
}

if (!function_exists('get_attendance_status_badge')) {
    function get_attendance_status_badge(string $status): string
    {
        $badges = [
            'hadir' => 'success',
            'telat' => 'warning',
            'izin' => 'info',
            'sakit' => 'danger',
            'cuti' => 'primary',
            'alpha' => 'secondary',
            'lembur' => 'dark',
        ];
        $badge = $badges[$status] ?? 'secondary';
        return '<span class="badge bg-' . $badge . '">' . ucfirst($status) . '</span>';
    }
}
