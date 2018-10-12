#!/bin/bash
sudo apt-get update -y
sudo apt-get upgrade -y
sudo apt-get install unzip
sudo apt-get install zip
sudo apt-get install postgresql postgresql-contrib -y
curl -o swarm_dump.gz http://115.146.93.42/swarm_dump.gz
gunzip swarm_dump.gz
sudo -u postgres psql -a -f prod_postgres_dump_07-09-2018_10_00_01.sql
sudo -u postgres psql -d swarm -a -f swarm_custom_tables.sql
sudo apt install apache2 -y
sudo ufw allow in "Apache Full"
sudo apt install -y php7.2
sudo apt install php7.2-pgsql -y
sudo chown ubuntu /etc/php/7.2/apache2/php.ini
echo 'extension=php_pgsql.dll' >> /etc/php/7.2/apache2/php.ini
sudo /etc/init.d/apache2 restart
sudo chown ubuntu /var/www/html/
sudo cp /home/ubuntu/SWARM/Deploy/swarm.zip /var/www/html
sudo cp /home/ubuntu/SWARM/Deploy/get_users.php /var/www/html
sudo chown ubuntu /var/www/html/get_users.php
cd /var/www/html
unzip swarm.zip
cd /home/ubuntu/SWARM/Deploy/
sudo chmod 777 /etc/postgresql/10/main/pg_hba.conf
sudo chmod 777 /etc/postgresql/10/main/postgresql.conf
echo 'host all all 0.0.0.0/0 trust' >> /etc/postgresql/10/main/pg_hba.conf
echo "listen_addresses = '*'" >> /etc/postgresql/10/main/postgresql.conf
sudo service postgresql stop
sudo service postgresql start
sudo sed -i '/host    all/d' /etc/postgresql/10/main/pg_hba.conf
sudo service postgresql stop
sudo service postgresql start
sudo apt-get install python-pip -y
sudo apt-get install python-psycopg2 -y
sudo apt-get install libpq-dev -y
sudo pip install -U textblob
sudo python Sentiments.py
sudo python IdentifyTagsMorphed.py
var="################ Package Deployed Successfully! Final URL: http://"
var+=$(curl ipinfo.io/ip)
var+="/swarm"
var+=" ################"
echo ""
echo ""
echo ""
echo ""
echo $var
#sed -i 's/\r//' Deployminimum.sh