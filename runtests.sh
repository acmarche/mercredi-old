#!/bin/bash

bin/console doctrine:fixtures:load --no-interaction

phpunit src/AcMarche/Mercredi/Admin/Tests/
phpunit src/AcMarche/Mercredi/Plaine/Tests/
phpunit src/AcMarche/Mercredi/Parent/Tests/
phpunit src/AcMarche/Mercredi/Front/Tests/
phpunit src/AcMarche/Mercredi/Animateur/Tests/
phpunit src/AcMarche/Mercredi/Ecole/Tests/
phpunit src/AcMarche/Mercredi/Security/Tests/
