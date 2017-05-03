#!/bin/bash
while :
do
	echo "Updating stats..."
        /usr/bin/php updateStats.php
	echo "...complete"
        sleep 30
done

