sudo apt-get update
sudo apt-get install unzip
wget -q https://www.postgresql.org/media/keys/ACCC4CF8.asc -O - | sudo apt-key add -
sudo sh -c 'echo "deb http://apt.postgresql.org/pub/repos/apt/ `lsb_release -cs`-pgdg main" >> /etc/apt/sources.list.d/pgdg.list'
sudo apt-get update
sudo apt-get install postgresql postgresql-contrib -y
#Download sql zip file "prod_postgres_dump_07-09-2018_10_00_01.sql.gz"
#Along with 3 SQL sCripts "swarm_custom_tables", "swarm_custom_tables", "swarm_scores_insert".
gunzip prod_postgres_dump_07-09-2018_10_00_01.sql.gz
sudo -u postgres psql postgres
\i prod_postgres_dump_07-09-2018_10_00_01.sql
\c swarm
\i swarm_custom_tables.sql
\i swarm_custom_tables.sql
\i swarm_scores_insert.sql
\q
#DB Configured
#Start Apache Installation
sudo apt update
sudo apt install apache2 -y
#sudo ufw app list
sudo ufw allow in "Apache Full"
#sudo ufw status
sudo apt-get install php5 libapache2-mod-php5 php5-mcrypt -y
#Download swarm.zip
sudo mv /home/ubuntu/swarm.zip /var/www/html
sudo chmod 777 /var/www/html/
unzip /var/www/html/swarm.zip /var/www/html/
cd /var/www/html
unzip swarm.zip
cd /home/ubuntu
sudo apt-get install php5-pgsql
#add extension=php_pgsql.dll in php.ini (/etc/php5/apache2/)