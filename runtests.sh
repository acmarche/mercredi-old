#!/bin/bash

bin/console doctrine:fixtures:load --no-interaction

bin/phpunit src/AcMarche/Mercredi/Admin/Tests/
bin/phpunit src/AcMarche/Mercredi/Plaine/Tests/
bin/phpunit src/AcMarche/Mercredi/Parent/Tests/
bin/phpunit src/AcMarche/Mercredi/Front/Tests/
bin/phpunit src/AcMarche/Mercredi/Animateur/Tests/
bin/phpunit src/AcMarche/Mercredi/Ecole/Tests/
bin/phpunit src/AcMarche/Mercredi/Security/Tests/
