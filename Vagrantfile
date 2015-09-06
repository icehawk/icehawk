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

end
