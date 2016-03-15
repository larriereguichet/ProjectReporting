all:install

install:
	composer install
	bin/console doctrine:database:create --if-not-exists
	make assets

assets:
	rsync -a vendor/components/jquery/jquery.min.js web/js/
	bin/console assets:install --symlink
