#!/bin/bash
# Monitor database connections continuously
# Usage: bash scripts/monitor-connections.sh [app-name]

APP_NAME=${1:-wizzypedia}
INTERVAL=10  # Check every 10 seconds
ALERT_THRESHOLD=120  # Alert if connections exceed this number

echo "========================================="
echo "DATABASE CONNECTION MONITOR"
echo "========================================="
echo "App: $APP_NAME"
echo "Checking every $INTERVAL seconds"
echo "Alert threshold: $ALERT_THRESHOLD connections"
echo "Press Ctrl+C to stop"
echo "========================================="
echo ""

while true; do
    echo "$(date '+%Y-%m-%d %H:%M:%S')"
    echo "-------------------"
    
    # Get connection count
    CONNECTION_INFO=$(heroku run "php -r '
        \$db = new mysqli(
            getenv(\"DB_SERVER\"),
            getenv(\"DB_USER\"),
            getenv(\"DB_PASS\"),
            getenv(\"DB_NAME\")
        );
        
        if (\$db->connect_error) {
            echo \"ERROR: Cannot connect to database\n\";
            exit(1);
        }
        
        // Get current connections
        \$result = \$db->query(\"SHOW STATUS LIKE '\''Threads_connected'\''\");
        \$row = \$result->fetch_row();
        \$current = \$row[1];
        
        // Get max connections
        \$result = \$db->query(\"SHOW VARIABLES LIKE '\''max_connections'\''\");
        \$row = \$result->fetch_row();
        \$max = \$row[1];
        
        // Get sleeping connections
        \$result = \$db->query(\"SELECT COUNT(*) FROM information_schema.processlist WHERE command = '\''Sleep'\''\");
        \$row = \$result->fetch_row();
        \$sleeping = \$row[0];
        
        echo \"Connections: \$current/\$max | Sleeping: \$sleeping\";
        
        if (\$current > $ALERT_THRESHOLD) {
            echo \" | ⚠️ ALERT: HIGH CONNECTION COUNT!\";
        }
        
        \$db->close();
    '" --app $APP_NAME 2>/dev/null)
    
    if [ $? -eq 0 ]; then
        echo "$CONNECTION_INFO"
    else
        echo "Failed to get connection info"
    fi
    
    # Check if we need to kill connections
    if [[ $CONNECTION_INFO == *"ALERT"* ]]; then
        echo ""
        echo "🚨 Running emergency connection cleanup..."
        heroku run "php maintenance/killIdleConnections.php --force" --app $APP_NAME
    fi
    
    echo ""
    sleep $INTERVAL
done
