<?php
/**
 * Database Connection Monitoring Script for MediaWiki
 * Run this to check current database connections and limits
 */

// Get database credentials from environment
$dbServer = getenv("DB_SERVER");
$dbName = getenv("DB_NAME");
$dbUser = getenv("DB_USER");
$dbPass = getenv("DB_PASS");

if (!$dbServer || !$dbName || !$dbUser || !$dbPass) {
    die("Error: Database environment variables not set.\n");
}

try {
    // Connect to database
    $dsn = "mysql:host=$dbServer;dbname=$dbName;charset=utf8mb4";
    $pdo = new PDO($dsn, $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== Database Connection Check ===\n";
    echo "Connected to: $dbServer\n";
    echo "Database: $dbName\n\n";
    
    // Check current connections
    echo "=== Current Connection Status ===\n";
    $stmt = $pdo->query("SHOW STATUS LIKE 'Threads_connected'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Current connections: " . $result['Value'] . "\n";
    
    // Check max connections
    $stmt = $pdo->query("SHOW VARIABLES LIKE 'max_connections'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Max connections allowed: " . $result['Value'] . "\n";
    
    // Check connection usage percentage
    $stmt = $pdo->query("SHOW STATUS LIKE 'Threads_connected'");
    $current = $stmt->fetch(PDO::FETCH_ASSOC)['Value'];
    $stmt = $pdo->query("SHOW VARIABLES LIKE 'max_connections'");
    $max = $stmt->fetch(PDO::FETCH_ASSOC)['Value'];
    $percentage = round(($current / $max) * 100, 2);
    echo "Connection usage: $percentage%\n\n";
    
    // Show processlist
    echo "=== Active Connections (Process List) ===\n";
    $stmt = $pdo->query("SHOW PROCESSLIST");
    $processes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Total processes: " . count($processes) . "\n";
    echo "Details:\n";
    foreach ($processes as $process) {
        echo sprintf(
            "  ID: %s | User: %s | Host: %s | DB: %s | Command: %s | Time: %s | State: %s\n",
            $process['Id'],
            $process['User'],
            substr($process['Host'], 0, 30),
            $process['db'] ?? 'NULL',
            $process['Command'],
            $process['Time'],
            $process['State'] ?? 'NULL'
        );
    }
    
    // Check for long-running queries
    echo "\n=== Long Running Queries (>5 seconds) ===\n";
    $longQueries = array_filter($processes, function($p) {
        return $p['Time'] > 5 && $p['Command'] !== 'Sleep';
    });
    
    if (empty($longQueries)) {
        echo "No long-running queries found.\n";
    } else {
        foreach ($longQueries as $query) {
            echo sprintf(
                "  Query running for %d seconds: %s\n",
                $query['Time'],
                substr($query['Info'] ?? 'N/A', 0, 100)
            );
        }
    }
    
    // Recommendations
    echo "\n=== Recommendations ===\n";
    if ($percentage > 80) {
        echo "⚠️  WARNING: Connection usage is above 80%!\n";
        echo "   - Consider upgrading your JawsDB plan for more connections\n";
        echo "   - Ensure connection pooling is properly configured\n";
        echo "   - Check for connection leaks in your application\n";
    } elseif ($percentage > 60) {
        echo "⚠️  CAUTION: Connection usage is above 60%\n";
        echo "   - Monitor closely for spikes\n";
        echo "   - Consider implementing caching to reduce DB load\n";
    } else {
        echo "✅ Connection usage is healthy (below 60%)\n";
    }
    
    // Check if persistent connections are being used
    echo "\n=== Connection Configuration Check ===\n";
    $persistentCount = 0;
    foreach ($processes as $process) {
        if (strpos($process['Host'], ':') !== false) {
            $persistentCount++;
        }
    }
    echo "Persistent connections detected: $persistentCount\n";
    
} catch (PDOException $e) {
    echo "ERROR: Database connection failed!\n";
    echo "Error message: " . $e->getMessage() . "\n\n";
    
    if (strpos($e->getMessage(), 'Too many connections') !== false) {
        echo "=== Too Many Connections Error Detected ===\n";
        echo "Possible solutions:\n";
        echo "1. Wait a few minutes for connections to timeout\n";
        echo "2. Restart your Heroku dynos: heroku restart\n";
        echo "3. Upgrade your JawsDB plan for more connections\n";
        echo "4. Ensure the new LocalSettings.php configuration is deployed\n";
    }
}

echo "\n=== Script Complete ===\n";
