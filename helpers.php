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

function connectToRedisSentinel(array $sentinelHosts, string $masterName, ?string $password = null): Redis {
    $redis = new Redis();
    
    foreach ($sentinelHosts as $sentinelHost) {
        [$host, $port] = explode(':', $sentinelHost);
        $port = (int)$port;
        
        try {
            $sentinel = new Redis();
            if (!$sentinel->connect($host, $port, 2.0)) {
                continue;
            }
            
            $masterInfo = $sentinel->rawCommand('SENTINEL', 'get-master-addr-by-name', $masterName);
            $sentinel->close();
            
            if ($masterInfo && is_array($masterInfo) && count($masterInfo) >= 2) {
                $masterHost = $masterInfo[0];
                $masterPort = (int)$masterInfo[1];
                
                if ($redis->connect($masterHost, $masterPort, 2.0)) {
                    if ($password) {
                        $redis->auth($password);
                    }
                    return $redis;
                }
            }
        } catch (Exception $e) {
            continue;
        }
    }
    
    throw new Exception("Failed to connect to Redis master via any Sentinel");
}
