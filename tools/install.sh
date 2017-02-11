#!/usr/bin/bash
REQPHPMODULES=()
REQPHPMODULES+=('mysql')
REQPHPMODULES+=('gd')
REQPHPMODULES+=('mbstring')
REQPHPMODULES+=('curl')
REQPHPMODULES+=('zlib')
REQPHPMODULES+=('ldap')

version(){
    local h t v

    [[ $2 = "$1" || $2 = "$3" ]] && return 0

    v=$(printf '%s\n' "$@" | sort -V)
    h=$(head -n1 <<<"$v")
    t=$(tail -n1 <<<"$v")

    [[ $2 != "$h" && $2 != "$t" ]]
}

check_webserver(){
    if [[ type "httpd" > /dev/null 2>&1 || type "apache2" > /dev/null 2>&1 ]]; then
        return 1
    elif type "nginx" > /dev/null 2>&1; then
        return 2
    else
        return 0
    fi
}

check_php_module(){
    MODULE=$1
#NOT FINISHED YET TODO::finish implementing this function
}
#Find OS
 if cat /etc/*release | grep ^NAME | grep CentOS; then
    OS="CentOS"
    PM="yum"
 elif cat /etc/*release | grep ^NAME | grep Red; then
    OS="RedHat"
    PM="yum"
 elif cat /etc/*release | grep ^NAME | grep Fedora; then
    OS="Fedora"
    PM="yum"
 elif cat /etc/*release | grep ^NAME | grep Ubuntu; then
    OS="Ubuntu"
    PM="apt"
 elif cat /etc/*release | grep ^NAME | grep Debian ; then
    OS="Debian"
    PM="apt"
 else
    echo "OS NOT SUPPORTED - Please perform a manual install"
    exit 1;
 fi
echo "This is the install script for MailWatch";echo;
echo "The script will attempt to install all of the required packages and configure them for you";echo;

check if mailscanner is installed
if [ ! -f "/etc/Mailscanner/Mailscanner.conf" ]; then
    echo "MailScanner config is missing";echo;
    echo "MailScanner must be installed before installing MailWatch";echo;
    echo "Aborting...";exit 1;
fi

#check for webserver
check_webserver
if [[ $? == 1 ]]; then
    WEBSERVER="apache"
elif [[ $? == 2 ]]; then
    WEBSERVER="nginx"
else
    echo "We're unable to find your webserver.  We support Apache and Nginx";echo;
    echo "Do you wish me to install a webserver?"
    echo "1 - Apache"
    echo "2 - Nginx"
    echo "N - do not install or configure"
    echo;
    read -r -p "Select Webserver: " response
    if [[ $response =~ ^([nN][oO])$ ]]; then
       #do not install or configure webserver
       WEBSERVER=
    elif [ $response == 1 ]; then
       #Apache
       WEBSERVER="apache"
       INSTALLWEBSERVER=1
    elif [ $response == 2 ]; then
       #Nginx
       WEBSERVER="nginx"
       INSTALLWEBSERVER=1
    else
       WEBSERVER=
    fi
fi

#check php is installed
if type "php" > /dev/null 2>&1; then
    #php is installed, lets check the version
    PHPMAX="5.9"
    PHPMIN="5.3"
    PHPVERSION=$(php -v|grep --only-matching --max-count=1 --perl-regexp "[5|7]\.\\d+\.\\d+")
    if ! version $PHPMIN $PHPVERSION $PHPMAX; then
        echo "Your PHP version ($PHPVERSION) is unsupported.  Supported PHP version between $PHPMIN and $PHPMAX";echo;
        echo "Please upgrade your PHP version, and then re-run this script";exit 1;
    else
        #php version installed and version ok
        PHP=1
    fi
else
    echo "PHP not installed";echo;
    echo "Would you like me to install PHP? ";echo;
    read -r -p "Would you like me to install PHP? <Y/n> " response
    if [[ $response =~ ^([nN][oO])$ ]]; then
        echo "PHP is required to continue.  Please manually install PHP and then re-run this install script";echo;
        echo "Aborting...";exit 1;
    else
        PHP=0
        INSTALLPHP=1
    fi
fi

#check mysql
if ! type "mysqld" > /dev/null 2>&1; then
    #mysqld missing
    echo "Mysql Server not found";echo;
    read -r -p "Would you like me to install mysql? <Y/n> " response
    if [[ $response =~ ^([nN][oO])$ ]]; then
        echo "MySQL is required to continue.  Please manually install MySQL and then re-run this install script";echo;
        echo "Aborting...";exit 1;
    else
        MYSQL=0
        INSTALLMYSQL=1
    fi
else
    #possible version checks needed here! TODO:version checks
    MYSQL=1
fi








