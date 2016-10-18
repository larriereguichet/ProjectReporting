all:install

install:
	composer install
	bin/console doctrine:database:create --if-not-exists
	make assets
	make install-ansible

assets:
	bin/console assets:install --symlink

install-server:
	ansible-playbook etc/ansible/install.yml --ask-become-pass

deploy:
	ansible-playbook etc/ansible/deploy.yml

rollback:
	ansible-playbook etc/ansible/rollback.yml
