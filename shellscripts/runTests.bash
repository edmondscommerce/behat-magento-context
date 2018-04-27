#!/usr/bin/env bash
source="${BASH_SOURCE[0]}"
dir="$( cd -P "$( dirname "$source" )" && pwd )"
cd $dir;
# Need to install unzip, java, chromedriver
# Need to start selenium in the back ground

function isInstalled() {
checkFor="$1"
    set +e
    if ! command -v "$checkFor"
    then
        echo 'false'
    fi
    echo 'true'
}

unzipInstalled=$(isInstalled unzip)
javaInstalled=$(isInstalled java)
chromeDriverInstalled=$(isInstalled chromedriver)
chromeInstalled=$(isInstalled google-chrome)
if [[ $unzipInstalled == 'false' ]]
then
    yum install unzip
fi
if [[ $javaInstalled == 'false' ]]
then
    yum install java
fi
if [[ $chromeDriverInstalled == 'false' ]]
then
    yum install chromedriver
fi
if [[ $chromeInstalled == 'false' ]]
then
#We need to install the font dependencies
yum install -y libXext fontconfig libfontconfig.so.1 liberation-mono-fonts  liberation-narrow-fonts liberation-sans-fonts  liberation-serif-fonts;

# Grab the RPM
wget https://dl.google.com/linux/direct/google-chrome-stable_current_x86_64.rpm

# Install the RPM
yum install -y google-chrome-stable_current_x86_64.rpm
fi


isSeleniumRunning=$(ps aux | grep se[l]enium | wc -l)

if [[ $isSeleniumRunning == 0 ]]
then
    ../bin/selenium-background-run.bash
fi

cd ../
pwd
./bin/phpunit -c ./phpunit.xml.dist ./tests --coverage-html /tmp/coverage

