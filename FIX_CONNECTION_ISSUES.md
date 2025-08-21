# MediaWiki Database Connection Fix - Deployment Guide

## Problem Identified
Your MediaWiki installation is exhausting the 150 connection limit on JawsDB Maria. This indicates a serious connection leak, likely caused by:
- Extensions creating multiple connections
- Job queue spawning excessive connections  
- No connection cleanup after requests
- Persistent connections not being properly reused

## Changes Made

### 1. LocalSettings.php
- **Disabled persistent connections** (they can leak in some PHP configs)
- **Disabled job runs on page requests** (`$wgJobRunRate = 0`)
- **Enabled miser mode** to reduce database operations
- **Added connection cleanup hook** to force-close connections after each request
- **Improved caching configuration** to reduce database hits

### 2. Apache Configuration (apache_app.conf)
- **Reduced MaxRequestWorkers to 5** (was 8)
- **Reduced MaxConnectionsPerChild to 50** (was 100)
- **Limited spare servers** to prevent connection multiplication

### 3. Monitoring Scripts Created
- `check_db_connections.php` - Monitor current connections
- `kill_idle_connections.php` - Kill stuck/idle connections
- `diagnose_connections.php` - Comprehensive diagnostic tool

## Deployment Steps

### Step 1: Deploy Changes
```bash
# Add all changes
git add LocalSettings.php apache_app.conf Procfile *.php FIX_CONNECTION_ISSUES.md

# Commit
git commit -m "Fix database connection exhaustion - disable persistent connections, add cleanup hooks"

# Push to Heroku
git push heroku master
```

### Step 2: Clear Existing Connections
```bash
# Restart all dynos to clear current connections
heroku restart --app wizzypedia

# Wait 30 seconds for restart to complete
sleep 30
```

### Step 3: Run Diagnostics
```bash
# Check current connection status
heroku run php diagnose_connections.php --app wizzypedia

# If connections are still high, kill idle ones
heroku run php kill_idle_connections.php --app wizzypedia
```

### Step 4: Monitor
```bash
# Check connections periodically
heroku run php check_db_connections.php --app wizzypedia

# Check application logs for errors
heroku logs --tail --app wizzypedia
```

## If Issues Persist

### Option 1: Emergency Connection Cleanup
```bash
# Kill all idle connections
heroku run php kill_idle_connections.php --app wizzypedia

# Force restart
heroku ps:restart --app wizzypedia
```

### Option 2: Disable Problematic Extensions
Edit LocalSettings.php and uncomment these lines:
```php
$wgDisableSearchUpdate = true;  # Disable search indexing
$wgEnableUploads = false;  # Temporarily disable uploads
```

### Option 3: Run Jobs Separately
Since we disabled job runs on page requests, set up a scheduler to run jobs:
```bash
# Add Heroku Scheduler add-on
heroku addons:create scheduler:standard --app wizzypedia

# Configure it to run every 10 minutes:
php maintenance/runJobs.php --maxjobs=20
```

### Option 4: Scale Down Temporarily
```bash
# Reduce to single dyno to minimize connections
heroku ps:scale web=1 --app wizzypedia
```

## Monitoring Commands

Check connection usage:
```bash
heroku run php check_db_connections.php --app wizzypedia
```

Full diagnostic:
```bash
heroku run php diagnose_connections.php --app wizzypedia
```

Kill idle connections:
```bash
heroku run php kill_idle_connections.php --app wizzypedia
```

## Expected Results
After deployment, you should see:
- Connection usage drop from 100% to under 40%
- Faster page loads due to caching
- No more "Too many connections" errors
- Stable performance under load

## Long-term Solutions
1. **Set up Redis caching** - Add Heroku Redis for better caching
2. **Use read replicas** - Distribute read queries
3. **Optimize slow queries** - Review and optimize database queries
4. **Regular maintenance** - Run maintenance scripts regularly

## Support
If issues persist after these changes:
1. Check JawsDB dashboard for connection graphs
2. Review Heroku metrics for traffic spikes  
3. Consider upgrading JawsDB plan if legitimate traffic requires more connections

## Rollback
If needed, revert changes:
```bash
git revert HEAD
git push heroku master
heroku restart --app wizzypedia
```
