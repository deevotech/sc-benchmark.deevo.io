#!/bin/bash
rm -rf /tmp/hfc/*
npm test -- simple -c ./benchmark/simple/config.json -n ./benchmark/simple/myfabric-remote.json
