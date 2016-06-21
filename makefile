all:install

install:
	composer install
	bin/console doctrine:database:create --if-not-exists
	make assets
	make install-ansible

assets:
	bin/console assets:install --symlink

install-ansible:
	sudo apt-get install python python-pip
	sudo pip install ansible
	ansible-galaxy install carlosbuenosvinos.ansistrano-deploy carlosbuenosvinos.ansistrano-rollback

install-server:
	ansible-playbook etc/ansible/playbooks/install.yml --ask-become-pass

deploy:
	ansible-playbook etc/ansible/playbooks/deploy.yml

rollback:
	ansible-playbook etc/ansible/playbooks/rollback.yml
