<?php
declare(strict_types=1);

/**
 * Simple IP Hit Counter using Redis
 * PHP 8.3 Compatible
 * 
 * This script counts how many times each IP address has accessed the page
 * and stores the count in Redis.
 */

// Redis connection configuration
$redisHost = getenv('REDIS_HOST') ?: 'redis-sentinel';
$redisPort = (int)(getenv('REDIS_PORT') ?: 6379);
$redisPassword = getenv('REDIS_PASSWORD') ?: null;

// Get client IP address
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

try {
    // Connect to Redis
    $redis = new Redis();
    $connected = $redis->connect($redisHost, $redisPort);
    
    if (!$connected) {
        throw new Exception("Failed to connect to Redis at {$redisHost}:{$redisPort}");
    }
    
    if ($redisPassword) {
        $redis->auth($redisPassword);
    }
    
    // Get client IP
    $clientIP = getClientIP();
    
    // Create Redis key for this IP
    $redisKey = "ip_counter:" . $clientIP;
    
    // Increment the counter for this IP
    $hitCount = $redis->incr($redisKey);
    
    // Set expiration to 24 hours (optional - remove if you want permanent storage)
    $redis->expire($redisKey, 86400);
    
    // Get total unique IPs
    $allKeys = $redis->keys("ip_counter:*");
    $uniqueIPs = count($allKeys);
    
    // Get total hits across all IPs
    $totalHits = 0;
    foreach ($allKeys as $key) {
        $totalHits += (int)$redis->get($key);
    }
    
    // Get system information
    $phpVersion = phpversion();
    $hostname = gethostname();
    $serverSoftware = $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown';
    $currentTime = date('Y-m-d H:i:s');
    $ordinalSuffix = getOrdinalSuffix($hitCount);
    
    // HTML Response
    header('Content-Type: text/html; charset=utf-8');
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Redis IP Hit Counter</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
        <div class="ascii-cat">
     /\_/\  
    ( o.o ) 
     > ^ <  
    /|   |\
   (_|   |_)</div>
        
        <div class="container">
            <h1>🎯 Redis IP Hit Counter</h1>
            
            <div class="welcome">
                <span class="emoji">👋</span> Welcome! I saw you for the <?php echo $hitCount . $ordinalSuffix; ?> time today! :D
            </div>
            
            <div class="stat">
                <strong>Your IP Address:</strong><br>
                <span class="ip"><?php echo htmlspecialchars($clientIP); ?></span>
            </div>
            
            <div class="stat">
                <strong>Your Visit Count:</strong><br>
                <span class="count"><?php echo $hitCount; ?></span>
            </div>
            
            <h2 style="color: #667eea; margin-top: 30px;">📊 Global Statistics</h2>
            
            <div class="info-grid">
                <div class="info-card">
                    <strong>Total Unique IPs</strong>
                    <?php echo $uniqueIPs; ?>
                </div>
                
                <div class="info-card">
                    <strong>Total Hits (All IPs)</strong>
                    <?php echo $totalHits; ?>
                </div>
            </div>
            
            <h2 style="color: #764ba2; margin-top: 30px;">🖥️ System Information</h2>
            
            <div class="info-grid">
                <div class="info-card">
                    <strong>🐘 PHP Version</strong>
                    <?php echo htmlspecialchars($phpVersion); ?>
                </div>
                
                <div class="info-card">
                    <strong>🏷️ Hostname / Pod Name</strong>
                    <?php echo htmlspecialchars($hostname); ?>
                </div>
                
                <div class="info-card">
                    <strong>🌐 Server Software</strong>
                    <?php echo htmlspecialchars($serverSoftware); ?>
                </div>
                
                <div class="info-card">
                    <strong>🕐 Current Time</strong>
                    <?php echo htmlspecialchars($currentTime); ?>
                </div>
                
                <div class="info-card">
                    <strong>🔴 Redis Server</strong>
                    <?php echo htmlspecialchars($redisHost . ':' . $redisPort); ?>
                </div>
                
                <div class="info-card">
                    <strong>💾 Redis Connection</strong>
                    <span style="color: #28a745;">✓ Connected</span>
                </div>
            </div>
            
            <div class="footer">
                <strong>💡 Tip:</strong> Refresh the page to increment your counter! Each IP is tracked separately and expires after 24 hours.
            </div>
        </div>
        
        <script src="script.js"></script>
    </body>
    </html>
    <?php
    
} catch (Exception $e) {
    // Error handling
    header('Content-Type: text/html; charset=utf-8');
    http_response_code(500);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Error</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
        <div class="container">
            <div class="error">
            <h1>❌ Redis Connection Error</h1>
            <p><strong>Error:</strong> <?php echo htmlspecialchars($e->getMessage()); ?></p>
            <p><strong>Redis Host:</strong> <?php echo htmlspecialchars($redisHost); ?></p>
            <p><strong>Redis Port:</strong> <?php echo htmlspecialchars($redisPort); ?></p>
        </div>
            <div class="info">
                <strong>System Info:</strong><br>
                PHP Version: <?php echo phpversion(); ?><br>
                Hostname: <?php echo gethostname(); ?><br>
                Time: <?php echo date('Y-m-d H:i:s'); ?>
            </div>
        </div>
    </body>
    </html>
    <?php
}
?>
