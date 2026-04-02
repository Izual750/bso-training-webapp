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
            <p><strong>Error:</strong> <?= escapeHtml($errorMessage) ?></p>
            <p><strong>Redis Host:</strong> <?= escapeHtml($redisHost) ?></p>
            <p><strong>Redis Port:</strong> <?= escapeHtml((string)$redisPort) ?></p>
        </div>
        <div class="info">
            <strong>System Info:</strong><br>
            PHP Version: <?= escapeHtml(phpversion()) ?><br>
            Hostname: <?= escapeHtml(gethostname() ?: 'Unknown') ?><br>
            Time: <?= escapeHtml(date('Y-m-d H:i:s')) ?>
        </div>
    </div>
</body>
</html>
