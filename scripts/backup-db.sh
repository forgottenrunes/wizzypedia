#!/bin/bash

# Check if WIZZYPEDIA_DB environment variable is set
if [ -z "${WIZZYPEDIA_DB}" ]; then
    echo "Error: The WIZZYPEDIA_DB environment variable is not set. Please set this variable and try again."
    exit 1
fi

# your mysql:// connection string
connection_string=${WIZZYPEDIA_DB}

# extract username, password, hostname, and database from connection string
username=$(echo $connection_string | cut -d/ -f3 | cut -d: -f1)
password=$(echo $connection_string | cut -d/ -f3 | cut -d: -f2 | cut -d@ -f1)
hostname=$(echo $connection_string | cut -d@ -f2 | cut -d/ -f1 | cut -d: -f1)
port=$(echo $connection_string | cut -d: -f4 | cut -d/ -f1)
database=$(echo $connection_string | cut -d/ -f4)

# Get the current date in YYYY-MM-DD format
current_date=$(date +%F)

# Define the backup filename
backup_filename="backups/${current_date}-backup.sql"

# run mysqldump
echo "Dumping to ${backup_filename}"
mysqldump --column-statistics=0 -u $username -p$password -h $hostname $database > $backup_filename

echo "Done"