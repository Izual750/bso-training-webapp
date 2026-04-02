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
            <span class="emoji">👋</span> Welcome! I saw you for the <?= $hitCount . $ordinalSuffix ?> time today! :D
        </div>
        
        <div class="stat">
            <strong>Your IP Address:</strong><br>
            <span class="ip"><?= escapeHtml($clientIP) ?></span>
        </div>
        
        <div class="stat">
            <strong>Your Visit Count:</strong><br>
            <span class="count"><?= $hitCount ?></span>
        </div>
        
        <h2 style="color: #667eea; margin-top: 30px;">📊 Global Statistics</h2>
        
        <div class="info-grid">
            <div class="info-card">
                <strong>Total Unique IPs</strong>
                <?= $uniqueIPs ?>
            </div>
            
            <div class="info-card">
                <strong>Total Hits (All IPs)</strong>
                <?= $totalHits ?>
            </div>
        </div>
        
        <h2 style="color: #764ba2; margin-top: 30px;">🖥️ System Information</h2>
        
        <div class="info-grid">
            <div class="info-card">
                <strong>🐘 PHP Version</strong>
                <?= escapeHtml($phpVersion) ?>
            </div>
            
            <div class="info-card">
                <strong>🏷️ Hostname / Pod Name</strong>
                <?= escapeHtml($hostname) ?>
            </div>
            
            <div class="info-card">
                <strong>🌐 Server Software</strong>
                <?= escapeHtml($serverSoftware) ?>
            </div>
            
            <div class="info-card">
                <strong>🕐 Current Time</strong>
                <?= escapeHtml($currentTime) ?>
            </div>
            
            <div class="info-card">
                <strong>🔴 Redis Server</strong>
                <?= escapeHtml($redisHost . ':' . $redisPort) ?>
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
    
    <script>
        window.visitCount = <?= $hitCount ?>;
    </script>
    <script src="script.js"></script>
</body>
</html>
