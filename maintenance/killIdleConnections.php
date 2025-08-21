<?php
/**
 * Kill idle database connections to free up connection pool
 * 
 * This script forcefully closes idle connections to prevent connection exhaustion
 * Run this when experiencing "Too many connections" errors
 * 
 * Usage: php maintenance/killIdleConnections.php [--force]
 */

require_once __DIR__ . '/Maintenance.php';

use MediaWiki\MediaWikiServices;

class KillIdleConnections extends Maintenance {
    public function __construct() {
        parent::__construct();
        $this->addDescription('Kill idle database connections to free up connection pool');
        $this->addOption('force', 'Force kill connections older than 5 seconds', false, false);
        $this->addOption('threshold', 'Time threshold in seconds for idle connections (default: 30)', false, true);
        $this->addOption('dry-run', 'Show what would be killed without actually killing', false, false);
    }

    public function execute() {
        global $wgDBserver, $wgDBname, $wgDBuser, $wgDBpassword;
        
        $threshold = $this->getOption('threshold', 30);
        $force = $this->hasOption('force');
        $dryRun = $this->hasOption('dry-run');
        
        if ($force) {
            $threshold = 5;
            $this->output("FORCE MODE: Killing connections idle for more than 5 seconds\n");
        }
        
        $this->output("Checking for idle database connections...\n");
        $this->output("Threshold: $threshold seconds\n\n");
        
        try {
            // Create a new connection specifically for this operation
            $conn = new mysqli(
                $wgDBserver,
                $wgDBuser,
                $wgDBpassword,
                $wgDBname
            );
            
            if ($conn->connect_error) {
                $this->error("Connection failed: " . $conn->connect_error . "\n");
                return false;
            }
            
            // Get current connections
            $result = $conn->query("SHOW PROCESSLIST");
            if (!$result) {
                $this->error("Failed to get process list\n");
                return false;
            }
            
            $totalConnections = 0;
            $idleConnections = 0;
            $killedConnections = 0;
            $connections = [];
            
            while ($row = $result->fetch_assoc()) {
                $totalConnections++;
                $connections[] = $row;
                
                // Check if connection is idle
                if (($row['Command'] == 'Sleep' || $row['Command'] == 'Query' && $row['Info'] == null) 
                    && $row['Time'] > $threshold) {
                    
                    $idleConnections++;
                    
                    $this->output(sprintf(
                        "  [%d] User: %s, Host: %s, DB: %s, Idle: %ds, State: %s\n",
                        $row['Id'],
                        $row['User'],
                        $row['Host'],
                        $row['db'] ?? 'null',
                        $row['Time'],
                        $row['Command']
                    ));
                    
                    if (!$dryRun) {
                        // Kill the connection
                        $killQuery = "KILL CONNECTION " . $row['Id'];
                        if ($conn->query($killQuery)) {
                            $killedConnections++;
                            $this->output("    -> Killed connection {$row['Id']}\n");
                        } else {
                            $this->output("    -> Failed to kill connection {$row['Id']}\n");
                        }
                    } else {
                        $this->output("    -> [DRY RUN] Would kill connection {$row['Id']}\n");
                    }
                }
            }
            
            $this->output("\n========================================\n");
            $this->output("Summary:\n");
            $this->output("  Total connections: $totalConnections\n");
            $this->output("  Idle connections: $idleConnections\n");
            
            if (!$dryRun) {
                $this->output("  Killed connections: $killedConnections\n");
            } else {
                $this->output("  Would kill: $idleConnections connections\n");
            }
            
            // Get max connections setting
            $maxResult = $conn->query("SHOW VARIABLES LIKE 'max_connections'");
            if ($maxResult && $row = $maxResult->fetch_assoc()) {
                $maxConnections = $row['Value'];
                $usage = round(($totalConnections / $maxConnections) * 100, 2);
                
                $this->output("\nConnection Pool Status:\n");
                $this->output("  Max connections: $maxConnections\n");
                $this->output("  Current usage: $totalConnections/$maxConnections ($usage%)\n");
                
                if ($usage > 80) {
                    $this->output("\n⚠️  WARNING: Connection pool usage is above 80%!\n");
                    $this->output("Consider:\n");
                    $this->output("  1. Running this script more frequently\n");
                    $this->output("  2. Reducing connection timeout in LocalSettings.php\n");
                    $this->output("  3. Enabling more aggressive caching\n");
                    $this->output("  4. Upgrading your database plan\n");
                }
            }
            
            // Show connection breakdown by user
            $this->output("\nConnections by User:\n");
            $userCounts = [];
            foreach ($connections as $conn_info) {
                $user = $conn_info['User'];
                if (!isset($userCounts[$user])) {
                    $userCounts[$user] = 0;
                }
                $userCounts[$user]++;
            }
            
            foreach ($userCounts as $user => $count) {
                $this->output("  $user: $count connections\n");
            }
            
            // Show connection breakdown by command
            $this->output("\nConnections by Command:\n");
            $commandCounts = [];
            foreach ($connections as $conn_info) {
                $command = $conn_info['Command'];
                if (!isset($commandCounts[$command])) {
                    $commandCounts[$command] = 0;
                }
                $commandCounts[$command]++;
            }
            
            foreach ($commandCounts as $command => $count) {
                $this->output("  $command: $count connections\n");
            }
            
            $conn->close();
            
            $this->output("\n✓ Connection cleanup completed\n");
            
            if (!$dryRun && $killedConnections > 0) {
                $this->output("\nNOTE: Killed connections will be automatically recreated as needed.\n");
                $this->output("This is normal and helps free up the connection pool.\n");
            }
            
        } catch (Exception $e) {
            $this->error("Error: " . $e->getMessage() . "\n");
            return false;
        }
        
        return true;
    }
}

$maintClass = KillIdleConnections::class;
require_once RUN_MAINTENANCE_IF_MAIN;
