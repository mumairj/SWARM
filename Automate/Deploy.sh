sudo apt-get upgrade -y
sudo apt-get update
sudo apt update
sudo apt-get install unzip
sudo apt-get install zip
#wget -q https://www.postgresql.org/media/keys/ACCC4CF8.asc -O - | sudo apt-key add -
#sudo sh -c 'echo "deb http://apt.postgresql.org/pub/repos/apt/ `lsb_release -cs`-pgdg main" >> /etc/apt/sources.list.d/pgdg.list'
#sudo apt-get update
sudo apt-get install postgresql postgresql-contrib -y
#Download sql zip file "prod_postgres_dump_07-09-2018_10_00_01.sql.gz"
#Along with 3 SQL sCripts "swarm_custom_tables", "swarm_custom_tables", "swarm_scores_insert".
gunzip prod_postgres_dump_07-09-2018_10_00_01.sql.gz
sudo -u postgres psql postgres
\i prod_postgres_dump_07-09-2018_10_00_01.sql
\c swarm
\i swarm_custom_tables.sql
\i swarm_scores_insert.sql
\q
#DB Configured
#Start Apache Installation
sudo apt install apache2 -y
#sudo ufw app list
sudo ufw allow in "Apache Full"
#sudo ufw status
sudo apt-get install python-software-properties
sudo add-apt-repository ppa:ondrej/php
sudo apt-get update
sudo apt-get install -y php7.2
#Download swarm.zip
sudo chown ubuntu /var/www/html/
sudo cp /home/ubuntu/hyperdisk/swarm.zip /var/www/html
cd /var/www/html
unzip swarm.zip
cd /home/ubuntu
sudo apt-get install php7.2-pgsql -y
#add extension=php_pgsql.dll in php.ini (/etc/php5/apache2/)
sudo chown ubuntu /etc/php/7.2/apache2/php.ini
echo 'extension=php_pgsql.dll' >> /etc/php/7.2/apache2/php.ini
sudo /etc/init.d/apache2 restart

#Working fine up till here...
sudo chown ubuntu /etc/postgresql/10/main/pg_hba.conf
sudo chown ubuntu /etc/postgresql/10/main/postgresql.conf
sudo sed -i '/host    all/d' /etc/postgresql/10/main/pg_hba.conf
echo 'host all all 0.0.0.0/0 trust' >> /etc/postgresql/10/main/pg_hba.conf
echo "listen_addresses = '*'" >> /etc/postgresql/10/main/postgresql.conf
sudo service postgresql stop
sudo service postgresql start
#Run python scripts now
cd /home/ubuntu/
sudo apt-get install python-pip -y
sudo apt-get install python-psycopg2 -y
sudo apt-get install libpq-dev -y
sudo pip install -U textblob
sudo python Sentiments.py
sudo python IdentifyTagsMorphed.py
