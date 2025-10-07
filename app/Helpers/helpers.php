<?php

if (!function_exists('formatPhoneUniversal')) {
    function formatPhoneUniversal($phone)
    {
        $phone = preg_replace('/\D/', '', $phone);

        // Normalisasi format jadi 62xxxxxxxxxx
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        } elseif (str_starts_with($phone, '620')) {
            $phone = '62' . substr($phone, 3);
        }

        if (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }

        // Masking nomor
        $plain = preg_replace('/^62/', '', $phone);
        $visibleStart = substr($plain, 0, 2);
        $visibleEnd = substr($plain, -2);
        $masked = $visibleStart . str_repeat('*', max(0, strlen($plain) - 4)) . $visibleEnd;

        return '(+62)' . $masked;
    }
}
