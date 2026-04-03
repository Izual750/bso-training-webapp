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

$sentinelHostsEnv = getenv('REDIS_SENTINEL_HOSTS') ?: '';
$masterName = getenv('REDIS_MASTER_NAME') ?: 'mymaster';
$redisPassword = getenv('REDIS_PASSWORD') ?: null;

try {
    if ($sentinelHostsEnv) {
        $sentinelHosts = array_map('trim', explode(',', $sentinelHostsEnv));
        $redis = connectToRedisSentinel($sentinelHosts, $masterName, $redisPassword);
        $connectionInfo = "Sentinel ({$masterName})";
    } else {
        $redisHost = getenv('REDIS_HOST') ?: 'redis';
        $redisPort = (int)(getenv('REDIS_PORT') ?: 6379);
        
        $redis = new Redis();
        $connected = $redis->connect($redisHost, $redisPort);
        
        if (!$connected) {
            throw new Exception("Failed to connect to Redis at {$redisHost}:{$redisPort}");
        }
        
        if ($redisPassword) {
            $redis->auth($redisPassword);
        }
        $connectionInfo = "{$redisHost}:{$redisPort}";
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
        'connectionInfo' => $connectionInfo,
    ]);
    
} catch (Exception $e) {
    header('Content-Type: text/html; charset=utf-8');
    http_response_code(500);
    
    renderTemplate(__DIR__ . '/templates/error.template.php', [
        'errorMessage' => $e->getMessage(),
        'connectionInfo' => $connectionInfo ?? 'Unknown',
    ]);
}
