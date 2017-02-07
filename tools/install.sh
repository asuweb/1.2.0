#!/usr/bin/env bash

#Find OS
 if cat /etc/*release | grep ^NAME | grep CentOS; then
    echo "==============================================="
    echo "CentOS is supported"
    echo "==============================================="
    PACKAGE_MANAGER=yum
 elif cat /etc/*release | grep ^NAME | grep Red; then
    echo "==============================================="
    echo "Installing packages $YUM_PACKAGE_NAME on RedHat"
    echo "==============================================="
    PACKAGE_MANAGER=yum
 elif cat /etc/*release | grep ^NAME | grep Fedora; then
    echo "================================================"
    echo "Installing packages $YUM_PACKAGE_NAME on Fedorea"
    echo "================================================"
    PACKAGE_MANAGER=yum
 elif cat /etc/*release | grep ^NAME | grep Ubuntu; then
    echo "==============================================="
    echo "Installing packages $DEB_PACKAGE_NAME on Ubuntu"
    echo "==============================================="
    apt-get update
    PACKAGE_MANAGER=apt
 elif cat /etc/*release | grep ^NAME | grep Debian ; then
    echo "==============================================="
    echo "Installing packages $DEB_PACKAGE_NAME on Debian"
    echo "==============================================="
    apt-get update
    PACKAGE_MANAGER=apt
 elif cat /etc/*release | grep ^NAME | grep Mint ; then
    echo "============================================="
    echo "Installing packages $DEB_PACKAGE_NAME on Mint"
    echo "============================================="
    apt-get update
    PACKAGE_MANAGER=apt
 elif cat /etc/*release | grep ^NAME | grep Knoppix ; then
    echo "================================================="
    echo "Installing packages $DEB_PACKAGE_NAME on Kanoppix"
    echo "================================================="
    apt-get update
    PACKAGE_MANAGER=apt
 else
    echo "OS NOT DETECTED, couldn't install package $PACKAGE"
    exit 1;
 fi

#check php exists
command -v php >/dev/null 2>&1 ||
    {
        echo "PHP not found - installing"
        if [[ $PACKAGE_MANAGER eq "yum" ]]
        then
            yum install -y php php-mysql php-mbcrypt php-gettext php-gd
        else
            apt-get install php5 php5-mysql php5-mbcrypt php5-gettext php5-gd
        fi
    }
command -v php >/dev/null 2>&1 || {echo "Unable to install PHP. Aborting" >&2; exit 1;}

