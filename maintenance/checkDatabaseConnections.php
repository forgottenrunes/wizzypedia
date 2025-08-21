<?php
/**
 * Check database connection status and provide diagnostics
 * 
 * Run this script to check current database connections and identify issues
 * Usage: php maintenance/checkDatabaseConnections.php
 */

require_once __DIR__ . '/Maintenance.php';

use MediaWiki\MediaWikiServices;

class CheckDatabaseConnections extends Maintenance {
    public function __construct() {
        parent::__construct();
        $this->addDescription('Check database connection status and provide diagnostics');
        $this->addOption('verbose', 'Show detailed connection information', false, false, 'v');
    }

    public function execute() {
        global $wgDBserver, $wgDBname, $wgDBuser;
        
        $this->output("Database Connection Diagnostics\n");
        $this->output("================================\n\n");
        
        // Basic database info
        $this->output("Database Server: $wgDBserver\n");
        $this->output("Database Name: $wgDBname\n");
        $this->output("Database User: $wgDBuser\n\n");
        
        try {
            $lb = MediaWikiServices::getInstance()->getDBLoadBalancer();
            $db = $lb->getConnection(DB_REPLICA);
            
            // Check if we can connect
            if ($db->ping()) {
                $this->output("✓ Database connection successful\n\n");
                
                // Get connection statistics
                if ($this->hasOption('verbose')) {
                    $result = $db->query("SHOW STATUS LIKE 'Threads_connected'");
                    $row = $result->fetchRow();
                    if ($row) {
                        $this->output("Current connections: {$row[1]}\n");
                    }
                    
                    $result = $db->query("SHOW VARIABLES LIKE 'max_connections'");
                    $row = $result->fetchRow();
                    if ($row) {
                        $this->output("Max connections allowed: {$row[1]}\n");
                    }
                    
                    // Show processlist if very verbose
                    $this->output("\nActive processes:\n");
                    $result = $db->query("SHOW PROCESSLIST");
                    $count = 0;
                    while ($row = $result->fetchRow()) {
                        $count++;
                        if ($count <= 10) {  // Show first 10 processes
                            $this->output("  Process {$row[0]}: {$row[1]}@{$row[2]} - {$row[3]} - {$row[4]}s\n");
                        }
                    }
                    if ($count > 10) {
                        $this->output("  ... and " . ($count - 10) . " more processes\n");
                    }
                }
                
                // Cache status
                $this->output("\nCache Configuration:\n");
                global $wgMainCacheType, $wgMessageCacheType, $wgParserCacheType;
                $cacheTypes = [
                    CACHE_NONE => 'CACHE_NONE (No caching)',
                    CACHE_DB => 'CACHE_DB (Database cache)',
                    CACHE_ACCEL => 'CACHE_ACCEL (APCu or similar)',
                    CACHE_MEMCACHED => 'CACHE_MEMCACHED'
                ];
                
                $this->output("  Main cache: " . ($cacheTypes[$wgMainCacheType] ?? 'Unknown') . "\n");
                $this->output("  Message cache: " . ($cacheTypes[$wgMessageCacheType] ?? 'Unknown') . "\n");
                $this->output("  Parser cache: " . ($cacheTypes[$wgParserCacheType] ?? 'Unknown') . "\n");
                
                // Check APCu availability
                if (function_exists('apcu_fetch')) {
                    $this->output("  ✓ APCu is available\n");
                    if (function_exists('apcu_cache_info')) {
                        $info = apcu_cache_info(true);
                        $this->output("    Memory: " . round($info['mem_size'] / 1024 / 1024, 2) . " MB used\n");
                        $this->output("    Entries: " . $info['num_entries'] . "\n");
                    }
                } else {
                    $this->output("  ✗ APCu is NOT available\n");
                }
                
                // Performance settings status
                $this->output("\nPerformance Settings:\n");
                global $wgMiserMode, $wgJobRunRate, $wgUseFileCache;
                $this->output("  Miser mode: " . ($wgMiserMode ? 'Enabled' : 'Disabled') . "\n");
                $this->output("  Job run rate: " . $wgJobRunRate . "\n");
                $this->output("  File cache: " . ($wgUseFileCache ? 'Enabled' : 'Disabled') . "\n");
                
            } else {
                $this->error("✗ Cannot connect to database\n");
            }
            
        } catch (Exception $e) {
            $this->error("Database connection error: " . $e->getMessage() . "\n");
            return false;
        }
        
        $this->output("\nRecommendations:\n");
        $this->output("1. If seeing 'Too many connections' errors, ensure caching is properly configured\n");
        $this->output("2. Consider upgrading your JawsDB plan for more connections\n");
        $this->output("3. Run job queue manually: php maintenance/runJobs.php\n");
        $this->output("4. Monitor with: heroku logs --tail\n");
        
        return true;
    }
}

$maintClass = CheckDatabaseConnections::class;
require_once RUN_MAINTENANCE_IF_MAIN;
