## Setup

	install virtualbox
	install vagrant

	vagrant plugin install vagrant-vbguest
	vagrant init
	vagrant up
	vagrant provision

	vagrant ssh
	sudo nano /etc/apache2/sites-available/default

	    <VirtualHost *:80>
	        DocumentRoot /var/www/web
	        <Directory /var/www/web/>
	            AllowOverride All
	        </Directory>
	    </VirtualHost>

	sudo service apache2 restart