TODO: add setup to Berksfile/cookbooks

## Setup

sudo gem install berkshelf

berks install

install virtualbox
install vagrant

vagrant plugin install vagrant-vbguest
vagrant plugin install vagrant-berkshelf
vagrant init
vagrant up
vagrant provision

vagrant ssh
sudo apt-get update
sudo apt-get install libapache2-mod-php5 php5-uuid
sudo a2enmod actions
sudo a2enmod autoindex

/etc/apache2/sites-available/default:

    <VirtualHost *:80>
        DocumentRoot /vagrant/web
        <Directory /vagrant/web/>
            AllowOverride All
        </Directory>
    </VirtualHost>

sudo service apache2 restart