<?php
declare(strict_types=1);

function getClientIP(): string {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (isset($_SERVER['HTTP_X_REAL_IP'])) {
        $ipaddress = $_SERVER['HTTP_X_REAL_IP'];
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    } else {
        $ipaddress = 'UNKNOWN';
    }
    return $ipaddress;
}

function getOrdinalSuffix(int $number): string {
    $suffix = 'th';
    if ($number % 10 == 1 && $number % 100 != 11) $suffix = 'st';
    elseif ($number % 10 == 2 && $number % 100 != 12) $suffix = 'nd';
    elseif ($number % 10 == 3 && $number % 100 != 13) $suffix = 'rd';
    return $suffix;
}

function renderTemplate(string $templateFile, array $data = []): void {
    extract($data);
    require $templateFile;
}

function escapeHtml(string $text): string {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}
