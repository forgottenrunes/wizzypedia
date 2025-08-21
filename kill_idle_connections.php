<?php
/**
 * Kill idle database connections to free up connection pool
 * Run this if you're experiencing connection exhaustion
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
    
    echo "=== Killing Idle Database Connections ===\n";
    echo "Connected to: $dbServer\n\n";
    
    // Get current connection count
    $stmt = $pdo->query("SHOW STATUS LIKE 'Threads_connected'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $initialConnections = $result['Value'];
    echo "Current connections: $initialConnections\n";
    
    // Get max connections
    $stmt = $pdo->query("SHOW VARIABLES LIKE 'max_connections'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $maxConnections = $result['Value'];
    echo "Max connections: $maxConnections\n\n";
    
    // Get process list
    $stmt = $pdo->query("SHOW FULL PROCESSLIST");
    $processes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $killedCount = 0;
    $skippedCount = 0;
    
    echo "=== Analyzing Connections ===\n";
    foreach ($processes as $process) {
        $id = $process['Id'];
        $user = $process['User'];
        $command = $process['Command'];
        $time = $process['Time'];
        $state = $process['State'] ?? '';
        $info = $process['Info'] ?? '';
        
        // Skip system processes
        if ($user === 'system user' || $user === 'event_scheduler') {
            continue;
        }
        
        // Determine if connection should be killed
        $shouldKill = false;
        $reason = '';
        
        // Kill sleeping connections older than 30 seconds
        if ($command === 'Sleep' && $time > 30) {
            $shouldKill = true;
            $reason = "Idle for {$time} seconds";
        }
        
        // Kill queries running longer than 60 seconds (except important ones)
        elseif ($command === 'Query' && $time > 60) {
            // Don't kill this script's own connection
            if (strpos($info, 'SHOW FULL PROCESSLIST') === false &&
                strpos($info, 'KILL') === false) {
                $shouldKill = true;
                $reason = "Query running for {$time} seconds";
            }
        }
        
        // Kill connections stuck in "Waiting" states
        elseif (strpos($state, 'Waiting') !== false && $time > 10) {
            $shouldKill = true;
            $reason = "Stuck in waiting state for {$time} seconds";
        }
        
        if ($shouldKill) {
            try {
                $killStmt = $pdo->prepare("KILL ?");
                $killStmt->execute([$id]);
                echo "✓ Killed connection $id (User: $user, Reason: $reason)\n";
                $killedCount++;
            } catch (PDOException $e) {
                echo "✗ Failed to kill connection $id: " . $e->getMessage() . "\n";
            }
        } else {
            if ($command !== 'Sleep' || $time > 5) {
                echo "  Kept connection $id (User: $user, Command: $command, Time: {$time}s)\n";
            }
            $skippedCount++;
        }
    }
    
    echo "\n=== Summary ===\n";
    echo "Connections killed: $killedCount\n";
    echo "Connections kept: $skippedCount\n";
    
    // Check new connection count
    sleep(1); // Wait a moment for connections to clear
    $stmt = $pdo->query("SHOW STATUS LIKE 'Threads_connected'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $finalConnections = $result['Value'];
    $freed = $initialConnections - $finalConnections;
    
    echo "\nConnections before: $initialConnections\n";
    echo "Connections after: $finalConnections\n";
    echo "Connections freed: $freed\n";
    
    $percentage = round(($finalConnections / $maxConnections) * 100, 2);
    echo "\nCurrent usage: $percentage% of maximum\n";
    
    if ($percentage > 80) {
        echo "\n⚠️  WARNING: Still above 80% capacity!\n";
        echo "Consider:\n";
        echo "- Restarting Heroku dynos: heroku restart\n";
        echo "- Checking for connection leaks in extensions\n";
        echo "- Reviewing slow queries\n";
    } else {
        echo "\n✅ Connection pool is healthy\n";
    }
    
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== Complete ===\n";
