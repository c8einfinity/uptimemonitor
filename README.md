# uptime_monitor
Service to monitor server uptime etc

# Installing ?

## MacOS

```bash
brew install php

```

# Running uptime monitor as a daemon


```
cd docker
docker build . -t uptimemonitor/daemon
```

```bash
sudo systemctl enable uptimemonitor.service

sudo systemctl start uptimemonitor.service


sudo systemctl disable uptimemonitor.service
```