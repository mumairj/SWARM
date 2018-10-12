sudo apt-get install unzip
sudo apt-get install zip
sudo apt-get install postgresql postgresql-contrib -y
sudo -u postgres psql -a -f prod_postgres_dump_07-09-2018_10_00_01.sql
sudo -u postgres psql -d swarm -a -f swarm_custom_tables.sql
sudo apt install apache2 -y
sudo ufw allow in "Apache Full"
sudo apt-get install -y php7.2
sudo apt-get install php7.2-pgsql -y
sudo chown ubuntu /etc/php/7.2/apache2/php.ini
echo 'extension=php_pgsql.dll' >> /etc/php/7.2/apache2/php.ini
sudo /etc/init.d/apache2 restart
sudo chown ubuntu /var/www/html/
sudo cp /home/ubuntu/hyperdisk/swarm.zip /var/www/html
cd /var/www/html
unzip swarm.zip
cd /home/ubuntu/hyperdisk/