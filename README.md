scc
===

Server control center

git clone --recursive git://github.com/fuel/fuel.git . --branch 1.7/develop
git submodule foreach git checkout 1.7/develop

// composer.json reguire "phpseclib/phpseclib": "0.3.*@dev"

php composer.phar selfupdate
php composer.phar install
