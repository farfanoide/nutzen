# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure(2) do |config|

  config.vm.box = 'ubuntu/trusty64'

  config.vm.network 'private_network', ip: '192.168.50.4'
  config.vm.network 'forwarded_port', guest: 80, host: 8080

  config.vm.provider 'virtualbox' do |vm|
    vm.memory = 1024
  end

  if Vagrant.has_plugin?('vagrant-cachier')
    config.cache.scope = :box

    config.cache.synced_folder_opts = {
      type: :nfs,
      mount_options: ['rw', 'vers=3', 'tcp', 'nolock']
    }
  end

  config.vm.provision :shell, path: 'provision/vm_setup.sh'
end
