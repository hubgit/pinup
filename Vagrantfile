Vagrant.configure("2") do |config|
  config.vm.box = "precise64"
  config.vm.box_url = "http://files.vagrantup.com/precise64.box"
  config.vm.network :forwarded_port, guest: 80, host: 8081
  config.vm.network :forwarded_port, guest: 9200, host: 9201
  config.vm.network :private_network, ip: "192.168.50.51"

  config.vm.synced_folder "./", "/var/www/", owner: "vagrant", group: "www-data", mount_options: ["dmode=775,fmode=664"]

  config.vm.provider :virtualbox do |vb|
    vb.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
    vb.customize ["modifyvm", :id, "--natdnsproxy1", "on"]
  end

  config.vm.provision :shell, :inline => "sudo apt-get update -y"
  config.vm.provision :shell, :inline => "sudo apt-get install libapache2-mod-php5 -y"
  config.vm.provision :shell, :inline => "sudo a2enmod rewrite"
  config.vm.provision :shell, :inline => "sudo service apache2 restart"
end
