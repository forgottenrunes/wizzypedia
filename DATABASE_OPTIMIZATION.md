# MediaWiki Database Optimization for JawsDB on Heroku

## CRITICAL: Connection Exhaustion with 150+ Connections

Your MediaWiki installation is experiencing severe database connection exhaustion despite having 150 available connections. This indicates:
- **Connection leak**: Connections not being properly closed
- **Zombie connections**: Idle connections not being released
- **Multiple services**: Other apps might be using the same database
- **Bot attacks**: Automated traffic creating excessive connections

## EMERGENCY FIXES FOR 150-CONNECTION EXHAUSTION

### Immediate Actions Required:

1. **Run Connection Killer Script:**
   ```bash
   heroku run php maintenance/killIdleConnections.php --force
   ```

2. **Emergency Fix Script:**
   ```bash
   heroku run bash scripts/fix-connections-emergency.sh
   ```

3. **Check for Connection Hogs:**
   ```bash
   heroku run php maintenance/killIdleConnections.php --dry-run
   ```

### Aggressive Optimizations Added:

- **Connection Pooling**: Limited to 5 concurrent connections per process
- **Timeouts**: Reduced to 3 seconds for faster connection release
- **Disabled Features**: Email notifications, external images temporarily disabled
- **No Persistent Connections**: Prevents connection hoarding
- **Forced Connection Limits**: Hard limits on API and query operations

## Solutions Implemented

### 1. **Caching Configuration** (LocalSettings.php)
- **APCu Caching**: Enabled APCu (if available) for main cache, message cache, parser cache, and session cache
- **Database Cache Fallback**: If APCu isn't available, uses database caching (better than no caching)
- **Session Optimization**: Moved sessions to object cache instead of database
- **Parser Cache**: Extended parser cache expiration to 24 hours

### 2. **Database Connection Optimization** (LocalSettings.php)
- **Persistent Connections**: Configured MySQL to use persistent connections
- **Job Queue**: Disabled automatic job execution on page requests (`$wgJobRunRate = 0`)
- **Miser Mode**: Enabled to reduce expensive operations
- **File Cache**: Enabled file caching for anonymous users

### 3. **Browser Caching** (.htaccess)
- Static assets cached for 1 month
- CSS/JS cached for 1 week  
- HTML pages cached for 1 hour
- Compression enabled for text content

### 4. **Monitoring Tools**
- Created `maintenance/checkDatabaseConnections.php` for diagnostics
- Created `scripts/run-jobs.sh` for manual job processing

## Deployment Steps

1. **Deploy the changes to Heroku:**
   ```bash
   git add -A
   git commit -m "Fix database connection limits for JawsDB"
   git push heroku master
   ```

2. **Clear MediaWiki caches after deployment:**
   ```bash
   heroku run php maintenance/update.php --skip-external-dependencies
   heroku run php maintenance/rebuildLocalisationCache.php --force
   ```

3. **Set up Heroku Scheduler for job queue:**
   ```bash
   # Add Heroku Scheduler addon (free)
   heroku addons:create scheduler:standard
   
   # Open scheduler dashboard
   heroku addons:open scheduler
   
   # Add a new job:
   # Command: bash scripts/run-jobs.sh
   # Frequency: Every hour or every 10 minutes depending on your needs
   ```

4. **Monitor database connections:**
   ```bash
   # Check current connections
   heroku run php maintenance/checkDatabaseConnections.php --verbose
   
   # Watch logs in real-time
   heroku logs --tail
   ```

## Additional Optimizations

### If Problems Persist:

1. **Upgrade JawsDB Plan**: Consider upgrading to a higher tier with more connections (e.g., Kitefin Shared: 40 connections)

2. **Add Redis/Memcached**: 
   ```bash
   heroku addons:create memcachier:dev
   ```
   Then update LocalSettings.php to use Memcached instead of APCu.

3. **Enable Cloudflare**: Use Cloudflare's free tier for additional caching and DDoS protection

4. **Reduce Extensions**: Disable unused MediaWiki extensions that query the database

## Monitoring Commands

```bash
# Check database status
heroku run php maintenance/checkDatabaseConnections.php

# View current configuration
heroku run php maintenance/eval.php
> print_r([$wgMainCacheType, $wgJobRunRate, $wgMiserMode]);

# Manual cache purge if needed
heroku run php maintenance/purgeCache.php

# Run jobs manually
heroku run php maintenance/runJobs.php
```

## Expected Results

After these optimizations:
- Database connections should stay well below the limit
- Page load times should improve significantly
- The "Too many connections" error should be resolved
- Anonymous users will experience faster page loads due to file caching

## Rollback

If you need to rollback these changes:
1. Remove the optimization sections from LocalSettings.php
2. Delete .htaccess file
3. Set `$wgMainCacheType = CACHE_NONE;` 
4. Set `$wgJobRunRate = 1;`
5. Redeploy to Heroku

## Diagnosing Connection Issues

### Check What's Using Your Connections:
```bash
# See all current connections
heroku run php maintenance/killIdleConnections.php --dry-run

# Check if multiple apps share the database
heroku config:get JAWSDB_MARIA_URL

# Monitor in real-time (locally)
watch -n 5 'heroku run php maintenance/killIdleConnections.php --dry-run --threshold 10'
```

### Common Culprits for 150+ Connection Exhaustion:

1. **Bot Traffic**: Check your access logs for bots/crawlers
   ```bash
   heroku logs --tail | grep -i "bot\|crawler\|spider"
   ```

2. **Multiple Apps**: Verify no other apps use the same database URL

3. **Extension Issues**: Some MediaWiki extensions can leak connections
   - VisualEditor (known to create multiple connections)
   - Cargo (database-heavy extension)
   - External data extensions

4. **PHP-FPM Settings**: On Heroku, each dyno can spawn multiple PHP processes

### Permanent Fix Options:

1. **CloudFlare**: Add free CloudFlare CDN to reduce direct hits
2. **Rate Limiting**: Add nginx rate limiting
3. **Separate Read Replicas**: Use JawsDB read replicas
4. **Connection Proxy**: Add PgBouncer or ProxySQL

## Support

For JawsDB connection limits by plan:
- Free: 10 connections
- Kitefin Shared: 40 connections  
- Leopardshark: 75 connections
- Whitetip: 125-150 connections
- Custom plans: 150+ connections

With 150 connections being exhausted, you likely need to:
1. Identify and fix the root cause (connection leak)
2. Add CloudFlare or similar CDN
3. Consider database clustering or sharding
