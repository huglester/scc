git clone --recursive git://github.com/fuel/fuel.git . --branch 1.7/develop

git submodule foreach git checkout 1.7/develop

// composer.json reguire "phpseclib/phpseclib": "0.3.*@dev"

php composer.phar selfupdate

php composer.phar install

rm -rf fuel/app

mkdir fuel/app

cd fuel/app

git clone git://github.com/krek-dev/scc.git .

nano config/production/db.php

nano config/production/server.php

cd ..

php oil refine install

php oil r migrate

admin@admin.com/admin123
