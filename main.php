<?php
declare(strict_types=1);

/**
 * Simple IP Hit Counter using Redis
 * PHP 8.3 Compatible
 * 
 * This script counts how many times each IP address has accessed the page
 * and stores the count in Redis.
 */

require_once __DIR__ . '/helpers.php';

$redisHost = getenv('REDIS_HOST') ?: 'redis-sentinel';
$redisPort = (int)(getenv('REDIS_PORT') ?: 6379);
$redisPassword = getenv('REDIS_PASSWORD') ?: null;

try {
    $redis = new Redis();
    $connected = $redis->connect($redisHost, $redisPort);
    
    if (!$connected) {
        throw new Exception("Failed to connect to Redis at {$redisHost}:{$redisPort}");
    }
    
    if ($redisPassword) {
        $redis->auth($redisPassword);
    }
    
    $clientIP = getClientIP();
    $redisKey = "ip_counter:" . $clientIP;
    $hitCount = $redis->incr($redisKey);
    $redis->expire($redisKey, 86400);
    
    $allKeys = $redis->keys("ip_counter:*");
    $uniqueIPs = count($allKeys);
    
    $totalHits = 0;
    foreach ($allKeys as $key) {
        $totalHits += (int)$redis->get($key);
    }
    
    $phpVersion = phpversion();
    $hostname = gethostname();
    $serverSoftware = $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown';
    $currentTime = date('Y-m-d H:i:s');
    $ordinalSuffix = getOrdinalSuffix($hitCount);
    
    header('Content-Type: text/html; charset=utf-8');
    
    renderTemplate(__DIR__ . '/templates/index.template.php', [
        'clientIP' => $clientIP,
        'hitCount' => $hitCount,
        'ordinalSuffix' => $ordinalSuffix,
        'uniqueIPs' => $uniqueIPs,
        'totalHits' => $totalHits,
        'phpVersion' => $phpVersion,
        'hostname' => $hostname,
        'serverSoftware' => $serverSoftware,
        'currentTime' => $currentTime,
        'redisHost' => $redisHost,
        'redisPort' => $redisPort,
    ]);
    
} catch (Exception $e) {
    header('Content-Type: text/html; charset=utf-8');
    http_response_code(500);
    
    renderTemplate(__DIR__ . '/templates/error.template.php', [
        'errorMessage' => $e->getMessage(),
        'redisHost' => $redisHost,
        'redisPort' => $redisPort,
    ]);
}
