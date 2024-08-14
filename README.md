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

Copy the `public_html/uptimemonitor.service` file to `/etc/systemd/system/`


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