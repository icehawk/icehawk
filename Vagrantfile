VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

  # box-config
  config.vm.box = "devops000"
  config.vm.box_url = "http://box.3wolt.de/devops000/"
  config.vm.box_check_update = true
  config.vm.box_version = "~> 1.0.0"

  # network-config
  config.vm.network "public_network", type: "dhcp"
  config.vm.boot_timeout = 600

  #config.vm.provider "virtualbox" do |v|
  #    v.gui = true
  #end

  # SSH-config
  config.ssh.username = "root"
  config.ssh.password = '\g}xr+e#p@g1'
  config.ssh.insert_key = true

  # hostname
  config.vm.hostname = "IceHawk"
  config.vm.post_up_message = "--\nWelcome to IceHawk!\n\nThis VM delivers:\n\Application under: http://www.icehawk.de\n\tDocumentation under: http://doc.icehawk.de\n\tTests under: http://test.icehawk.de\n\tPMA under: http://pma.icehawk.de\n\n--\n"

  # provisioners
  # ------------

  # nginx configs, copy and link
  config.vm.provision "file", source: "env/nginx/doc.conf", destination: "/etc/nginx/sites-available/doc"
  config.vm.provision "file", source: "env/nginx/test.conf", destination: "/etc/nginx/sites-available/test"
  config.vm.provision "file", source: "env/nginx/dist.conf", destination: "/etc/nginx/sites-available/dist"
  config.vm.provision "file", source: "env/nginx/pma.conf", destination: "/etc/nginx/sites-available/pma"
  config.vm.provision "file", source: "env/vagrant/id_rsa", destination: "/root/.ssh/id_rsa"
  config.vm.provision "file", source: "env/vagrant/ssh_config", destination: "/root/.ssh/config"

  # shell commands
  config.vm.provision "shell", path: "env/vagrant/bootstrap.sh"

end
