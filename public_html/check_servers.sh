#!/bin/bash

# Bash script to execute the checking every 10 seconds. This will be called by a cron job but cron can only run every minute.
URL="https://uptimemonitor.ai/api/servermonitors/check";

curl -k "$URL"

sleep 10

curl -k "$URL"

sleep 10

curl -k "$URL"

sleep 10

curl -k "$URL"

sleep 10

curl -k "$URL"

sleep 10

curl -k "$URL"