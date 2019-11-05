#!/bin/bash

bin/console doctrine:fixtures:load --no-interaction

vendor/bin/simple-phpunit src/AcMarche/Mercredi/Admin/Tests/
vendor/bin/simple-phpunit src/AcMarche/Mercredi/Plaine/Tests/
vendor/bin/simple-phpunit src/AcMarche/Mercredi/Parent/Tests/
vendor/bin/simple-phpunit src/AcMarche/Mercredi/Front/Tests/
vendor/bin/simple-phpunit src/AcMarche/Mercredi/Animateur/Tests/
vendor/bin/simple-phpunit src/AcMarche/Mercredi/Ecole/Tests/
vendor/bin/simple-phpunit src/AcMarche/Mercredi/Security/Tests/
