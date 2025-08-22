<?php
/**
 * Redis Configuration for Production (Heroku)
 * 
 * This file should be included in LocalSettings.php when Redis is available
 * 
 * Heroku Redis provides a REDIS_URL environment variable in format:
 * redis://h:password@host:port
 */

// Only configure Redis if REDIS_URL is set (from Heroku Redis addon)
if (getenv('REDIS_URL')) {
    // Parse the Redis URL from Heroku
    $redisUrl = parse_url(getenv('REDIS_URL'));
    
    if ($redisUrl) {
        // Configure Redis for MediaWiki (without PHP Redis extension)
        // Use RESTBagOStuff which doesn't require the Redis PHP extension
        $wgObjectCaches['redis'] = [
            'class' => 'RESTBagOStuff',
            'url' => getenv('REDIS_URL'),
            'httpParams' => [
                'timeout' => 3,
                'connectTimeout' => 2,
            ],
        ];
        
        // Use Redis for all caching
        $wgMainCacheType = 'redis';
        $wgSessionCacheType = 'redis';      # Store sessions in Redis
        $wgMessageCacheType = 'redis';      # Store message cache in Redis  
        $wgParserCacheType = 'redis';       # Store parser cache in Redis
        
        // Configure session handling
        $wgSessionProviders = [
            [
                'class' => MediaWiki\Session\CookieSessionProvider::class,
                'args' => [[
                    'priority' => 30,
                    'callUserSetCookiesHook' => true,
                ]]
            ],
        ];
        
        // Job queue configuration - use Redis
        $wgJobTypeConf['default'] = [
            'class' => 'JobQueueRedis',
            'redisServer' => $redisUrl['host'] . ':' . $redisUrl['port'],
            'redisConfig' => [
                'connectTimeout' => 2,
                'password' => isset($redisUrl['pass']) ? $redisUrl['pass'] : null,
            ],
            'daemonized' => true,
        ];
        
        // Enable performance optimizations
        $wgEnableSidebarCache = true;
        $wgUseLocalMessageCache = true;
        
        // Enable Miser Mode for better performance
        $wgMiserMode = true;
        
        error_log("Redis caching enabled for MediaWiki using " . $redisUrl['host']);
    }
} else {
    // Fallback to basic caching if Redis is not available
    error_log("Redis URL not found, using default cache settings");
    // Keep existing cache settings from LocalSettings.php
}

// Database connection optimization (works with or without Redis)
$wgDBservers = false;  # Single server mode
$wgDBconnection_timeout = 5;  # Lower timeout for faster failure detection
