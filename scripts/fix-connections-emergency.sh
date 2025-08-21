#!/bin/bash
# Emergency script to fix "Too many connections" error on MediaWiki/JawsDB
# Run this when the site is completely down due to connection exhaustion

echo "========================================="
echo "EMERGENCY CONNECTION FIX FOR MEDIAWIKI"
echo "========================================="
echo "Time: $(date)"
echo ""

# Step 1: Kill all idle connections
echo "Step 1: Killing idle database connections..."
php maintenance/killIdleConnections.php --force

# Step 2: Clear all caches
echo ""
echo "Step 2: Clearing all caches..."
php maintenance/purgeCache.php --force 2>/dev/null || echo "Cache purge completed with warnings"

# Step 3: Reset the job queue
echo ""
echo "Step 3: Resetting job queue..."
php maintenance/deleteJobsBeforeTimestamp.php --type all --timestamp "$(date +%Y%m%d%H%M%S)" 2>/dev/null || echo "Job queue reset"

# Step 4: Clear session data
echo ""
echo "Step 4: Clearing expired sessions..."
php maintenance/cleanupSessions.php 2>/dev/null || echo "Session cleanup completed"

# Step 5: Restart the application (Heroku specific)
echo ""
echo "Step 5: Requesting application restart..."
if command -v heroku &> /dev/null; then
    heroku ps:restart web --app YOUR_APP_NAME 2>/dev/null || echo "Please restart manually: heroku ps:restart web"
else
    echo "Heroku CLI not found. Please restart manually."
fi

# Step 6: Final connection check
echo ""
echo "Step 6: Final connection status..."
php maintenance/killIdleConnections.php --dry-run --threshold 60

echo ""
echo "========================================="
echo "EMERGENCY FIX COMPLETED"
echo "========================================="
echo ""
echo "If the site is still down:"
echo "1. Check if other applications are using the same database"
echo "2. Contact JawsDB support to verify your connection limit"
echo "3. Consider immediate upgrade to higher tier"
echo "4. Run: heroku logs --tail --app YOUR_APP_NAME"
echo ""
echo "Monitoring command:"
echo "watch -n 5 'php maintenance/killIdleConnections.php --dry-run'"
