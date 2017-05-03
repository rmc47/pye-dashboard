#!/bin/bash
while :
do
        echo "Starting log upload..."
        /usr/bin/php uploadLogs.php
        echo "...crashed, restarting in 30s..."
        sleep 30
done
