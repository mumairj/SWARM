!/bin/bash
sudo apt-get update
sudo apt-get install git -y
git clone https://github.com/mumairj/SWARM.git
cd SWARM/Deploy/
sudo chmod 777 Deployminimum.sh
sh ./Deployminimum.sh