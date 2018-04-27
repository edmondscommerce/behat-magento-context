#!/usr/bin/env bash
readonly DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )";
cd $DIR;
set -e
set -u
set -o pipefail

isSeleniumRunning=$(ps aux | grep se[l]enium | wc -l)

if [[ $isSeleniumRunning == 0 ]]
then
    echo "Starting up Selenium"
    ../bin/selenium-background-run.bash
fi

cd ../

echo "Disabling xDebug"
phpenv config-rm xdebug.ini

echo "Running the tests"
./bin/phpunit -c ./phpunit.xml.dist ./tests

