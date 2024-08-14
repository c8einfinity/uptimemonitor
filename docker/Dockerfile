FROM ubuntu:22.04

ENV DEBIAN_FRONTEND=noninteractive

RUN apt update -y
RUN apt install -y php

RUN mkdir /home/uptimemonitor

WORKDIR /home/uptimemonitor

COPY uptimemonitor.service /home/uptimemonitor

CMD ["/sbin/init", "service start uptimemonitor"]