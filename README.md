# uptime_monitor
Service to monitor server uptime etc

# Installing ?

## MacOS

```bash
brew install php

```

# Running uptime monitor as a daemon

### Development
```
php bin/tina4service
```

### Production

Setup a cron job that calls the check_servers.sh file. This file checks the servers every 10 seconds. Change the sleep inside the file to check more often or less often.

```
chmod +x \home\uptimemonitor\public_html\check_servers.sh
```

```
* * * * * /home/uptimemonitor/public_html/check_servers.sh
```

Copy the `public_html/uptimemonitor.service` file to `/etc/systemd/system/`

### Make the php fpm file executable
```
chmod +x /path/to/run_php_fpm.sh
```

```bash
sudo systemctl enable uptimemonitor.service
```

```
sudo systemctl start uptimemonitor.service
```

```
sudo systemctl status uptimemonitor.service
```


```bash
sudo systemctl disable uptimemonitor.service
```

run every so many seconds ...
```dotenv
TINA4_SERVICE_TIME=5
```