#!/bin/bash
#chown -R www-data:www-data /opt/gopath/src/github.com/hyperledger/caliper/src/gui/www
#chmod -R g+w /opt/gopath/src/github.com/hyperledger/caliper/src/gui/www
#sudo chmod 777 -R /opt/gopath/src/github.com/hyperledger/caliper/src/gui/www/
cd /opt/gopath/src/github.com/hyperledger/caliper/
rm -rf /tmp/hfc/*
rm -rf .nyc_output/*
sleep 5 
npm test -- simple -c ./benchmark/simple/config.json -n ./benchmark/simple/myfabric-remote.json
