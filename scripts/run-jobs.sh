#!/bin/bash
# MediaWiki Job Queue Runner for Heroku
# Run this as a scheduled task (Heroku Scheduler) to process jobs without affecting page loads

echo "Starting MediaWiki job queue processing..."
echo "Time: $(date)"

# Run pending jobs with a limit to prevent timeout
php maintenance/runJobs.php --maxjobs=50 --maxtime=300

echo "Job queue processing completed at $(date)"

# Optional: Clear expired cache entries
echo "Clearing expired cache entries..."
php maintenance/purgeExpiredUserrights.php
php maintenance/purgeExpiredBlocks.php

echo "Maintenance tasks completed."
