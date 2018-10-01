#!/bin/bash
#chown -R www-data:www-data /opt/gopath/src/github.com/hyperledger/caliper/src/gui/www
#chmod -R g+w /opt/gopath/src/github.com/hyperledger/caliper/src/gui/www
#sudo chmod 777 -R /opt/gopath/src/github.com/hyperledger/caliper/src/gui/www/
KEY_PATH=/home/datlv/Documents/deevo/key/dev-full-rights.pem;

cd /opt/gopath/src/github.com/hyperledger/caliper/
rm -rf /tmp/hfc/*
rm -rf .nyc_output/*
# run log cpu and memory from peer, ordering service node, orderer
n=1 ; eval a$n="13.229.67.40"
n=2 ; eval a$n="13.250.109.169"
n=3 ; eval a$n="13.229.125.149"
n=4 ; eval a$n="54.179.181.86"
n=5 ; eval a$n="54.254.189.216"
n=6 ; eval a$n="18.136.126.89"

for i in 1 2 3 4 5
do
    eval ip=\$a$i
    echo ${ip}
   ssh -p 22 -i ${KEY_PATH}  ubuntu@${ip} 'cd /opt/gopath/src/github.com/deevotech/supply-chain-network/scripts ; ./logstash -n peer; ./logstash -n java; exit; '
done
ssh -p 22 -i ${KEY_PATH}  ubuntu@18.136.126.89 'cd /opt/gopath/src/github.com/deevotech/supply-chain-network/scripts ; ./logstash -n orderer; ./logstash -n java; exit; '
sleep 5 
npm test -- simple -c ./benchmark/simple/config.json -n ./benchmark/simple/myfabric-remote.json
