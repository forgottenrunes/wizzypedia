<?php
/**
 * Comprehensive diagnostic for MediaWiki connection issues
 * This will help identify what's causing connection exhaustion
 */

// Get database credentials from environment
$dbServer = getenv("DB_SERVER");
$dbName = getenv("DB_NAME");
$dbUser = getenv("DB_USER");
$dbPass = getenv("DB_PASS");

if (!$dbServer || !$dbName || !$dbUser || !$dbPass) {
    die("Error: Database environment variables not set.\n");
}

echo "=== MediaWiki Database Connection Diagnostic ===\n";
echo "Database: $dbName on $dbServer\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n\n";

try {
    // Connect to database
    $dsn = "mysql:host=$dbServer;dbname=$dbName;charset=utf8mb4";
    $pdo = new PDO($dsn, $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 1. Check connection limits and current usage
    echo "=== Connection Status ===\n";
    $stats = [];
    $queries = [
        'max_connections' => "SHOW VARIABLES LIKE 'max_connections'",
        'max_user_connections' => "SHOW VARIABLES LIKE 'max_user_connections'",
        'threads_connected' => "SHOW STATUS LIKE 'Threads_connected'",
        'threads_running' => "SHOW STATUS LIKE 'Threads_running'",
        'aborted_connects' => "SHOW STATUS LIKE 'Aborted_connects'",
        'connection_errors_max_connections' => "SHOW STATUS LIKE 'Connection_errors_max_connections'"
    ];
    
    foreach ($queries as $key => $query) {
        $stmt = $pdo->query($query);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats[$key] = $result['Value'] ?? 0;
    }
    
    echo "Max connections allowed: {$stats['max_connections']}\n";
    echo "Max per-user connections: {$stats['max_user_connections']}\n";
    echo "Currently connected: {$stats['threads_connected']}\n";
    echo "Currently running queries: {$stats['threads_running']}\n";
    echo "Failed connection attempts: {$stats['aborted_connects']}\n";
    echo "Times hit max connections: {$stats['connection_errors_max_connections']}\n";
    
    $usage = round(($stats['threads_connected'] / $stats['max_connections']) * 100, 2);
    echo "\nConnection usage: {$usage}%\n";
    
    // 2. Analyze connection sources
    echo "\n=== Connection Analysis by Host ===\n";
    $stmt = $pdo->query("
        SELECT 
            SUBSTRING_INDEX(host, ':', 1) as host_ip,
            COUNT(*) as connection_count,
            GROUP_CONCAT(DISTINCT command) as commands,
            AVG(time) as avg_time,
            MAX(time) as max_time
        FROM information_schema.processlist
        WHERE user = '$dbUser'
        GROUP BY SUBSTRING_INDEX(host, ':', 1)
        ORDER BY connection_count DESC
    ");
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($results as $row) {
        echo "Host: {$row['host_ip']}\n";
        echo "  Connections: {$row['connection_count']}\n";
        echo "  Commands: {$row['commands']}\n";
        echo "  Avg time: " . round($row['avg_time'], 2) . "s\n";
        echo "  Max time: {$row['max_time']}s\n";
    }
    
    // 3. Check for long-running queries
    echo "\n=== Long Running Queries (>10 seconds) ===\n";
    $stmt = $pdo->query("
        SELECT id, user, host, command, time, state, 
               LEFT(info, 100) as query_preview
        FROM information_schema.processlist
        WHERE time > 10 
        AND command != 'Sleep'
        ORDER BY time DESC
        LIMIT 10
    ");
    
    $longQueries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($longQueries)) {
        echo "No long-running queries found.\n";
    } else {
        foreach ($longQueries as $query) {
            echo "ID {$query['id']}: {$query['command']} for {$query['time']}s\n";
            if ($query['query_preview']) {
                echo "  Query: {$query['query_preview']}...\n";
            }
        }
    }
    
    // 4. Check for sleeping connections
    echo "\n=== Sleeping Connections ===\n";
    $stmt = $pdo->query("
        SELECT 
            COUNT(*) as sleep_count,
            AVG(time) as avg_sleep_time,
            MAX(time) as max_sleep_time
        FROM information_schema.processlist
        WHERE command = 'Sleep'
    ");
    
    $sleepStats = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Sleeping connections: {$sleepStats['sleep_count']}\n";
    echo "Average sleep time: " . round($sleepStats['avg_sleep_time'], 2) . "s\n";
    echo "Max sleep time: {$sleepStats['max_sleep_time']}s\n";
    
    // 5. Check MediaWiki-specific tables for issues
    echo "\n=== MediaWiki Table Analysis ===\n";
    
    // Check job queue size (can cause connection spikes)
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as job_count FROM job");
        $jobCount = $stmt->fetch(PDO::FETCH_ASSOC)['job_count'];
        echo "Pending jobs in queue: $jobCount\n";
        
        if ($jobCount > 1000) {
            echo "  ⚠️ High job count can cause connection spikes!\n";
        }
    } catch (Exception $e) {
        echo "Could not check job queue\n";
    }
    
    // Check recent changes (high activity indicator)
    try {
        $stmt = $pdo->query("
            SELECT COUNT(*) as changes 
            FROM recentchanges 
            WHERE rc_timestamp > DATE_SUB(NOW(), INTERVAL 1 HOUR)
        ");
        $recentChanges = $stmt->fetch(PDO::FETCH_ASSOC)['changes'];
        echo "Recent changes (last hour): $recentChanges\n";
    } catch (Exception $e) {
        echo "Could not check recent changes\n";
    }
    
    // 6. Recommendations
    echo "\n=== Recommendations ===\n";
    
    if ($usage > 80) {
        echo "🔴 CRITICAL: Connection usage above 80%!\n";
        echo "   Immediate actions:\n";
        echo "   1. Run: php kill_idle_connections.php\n";
        echo "   2. Restart dynos: heroku restart\n";
        echo "   3. Check for runaway scripts or bots\n";
    } elseif ($usage > 60) {
        echo "🟡 WARNING: Connection usage above 60%\n";
        echo "   Monitor closely and consider:\n";
        echo "   1. Enabling more aggressive caching\n";
        echo "   2. Reducing Apache MaxRequestWorkers\n";
    } else {
        echo "🟢 OK: Connection usage is acceptable\n";
    }
    
    if ($sleepStats['sleep_count'] > 50) {
        echo "\n⚠️ High number of sleeping connections detected\n";
        echo "   This indicates connections not being properly closed\n";
    }
    
    if ($stats['connection_errors_max_connections'] > 0) {
        echo "\n⚠️ Has hit max connections {$stats['connection_errors_max_connections']} times\n";
        echo "   This confirms connection exhaustion is occurring\n";
    }
    
    // 7. Check PHP configuration
    echo "\n=== PHP Configuration Check ===\n";
    echo "PHP Version: " . PHP_VERSION . "\n";
    echo "PDO MySQL driver: " . (extension_loaded('pdo_mysql') ? 'Loaded' : 'Not loaded') . "\n";
    echo "MySQLi extension: " . (extension_loaded('mysqli') ? 'Loaded' : 'Not loaded') . "\n";
    
    // Check if persistent connections are actually disabled
    $persistentCheck = ini_get('mysql.allow_persistent');
    echo "MySQL persistent connections: " . ($persistentCheck ? 'Enabled' : 'Disabled') . "\n";
    
} catch (PDOException $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    
    if (strpos($e->getMessage(), 'Too many connections') !== false) {
        echo "\n=== Connection Limit Reached ===\n";
        echo "The database is currently rejecting new connections.\n";
        echo "This confirms the issue is active right now.\n";
        echo "\nEmergency steps:\n";
        echo "1. Wait 2-3 minutes for connections to timeout\n";
        echo "2. Run: heroku restart --app your-app-name\n";
        echo "3. If persists, contact JawsDB support\n";
    }
}

echo "\n=== Diagnostic Complete ===\n";
echo "Generated at: " . date('Y-m-d H:i:s') . "\n";
