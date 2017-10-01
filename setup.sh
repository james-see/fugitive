#!/bin/bash
# installer for fugitive
# check OS

GREEN='\033[0;32m'
NC='\033[0m'
CYAN='\033[0;36m'
YELLOW='\033[1;33m'
LIGHTRED='\033[1;31m'
WHITE='\033[1;37m'
clear
echo -e "*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*\n* ${WHITE}welcome to ${GREEN}fugitive${WHITE} installer${NC} *\n*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*"
echo -e "\n press any key when ready \n"
if [ -f ".ready" ]; 
  then 
while true; do
    read -e -n1 -p "Looks like you already ran this setup program. Run again? (y/n): " yn
    case $yn in
        [Yy]* ) break;;
        [Nn]* ) exit;;
        * ) echo "Please answer yes or no.";;
    esac
done

fi

for (( i=5; i>0; i--)); do
    printf "\rHit any key to start, or starting automatically in $i seconds...\n"
    read -s -n 1 -t 1 key
    if [ $? -eq 0 ]
    then
        was_key_pressed="true"
        break
    fi
done
echo -e "\n$LIGHTRED[$YELLOW*$LIGHTRED]$WHITE CHECKING OS: $LIGHTRED[$YELLOW*$LIGHTRED]$NC\n"
sleep 1
echo "looks like you are running..."
sleep 1
OS="`uname`"
case $OS in
  'Linux')
    OS='Linux'
    alias ls='ls --color=auto'
    ;;
  'FreeBSD')
    OS='FreeBSD'
    alias ls='ls -G'
    ;;
  'WindowsNT')
    OS='Windows'
    echo "Use a different OS, Windows sucks. Sorry, not sorry."
    exit 1
    ;;
  'Darwin') 
    OS='OSX'
    ;;
  'SunOS')
    OS='Solaris'
    ;;
  'AIX') ;;
  *) ;;
esac
echo -e "$CYAN$OS$NC";
echo -e "\n$LIGHTRED[$YELLOW*$LIGHTRED]$WHITE CHECKING FOR PHP 7+ $LIGHTRED[$YELLOW*$LIGHTRED]$NC\n";
sleep 1
#echo "checking for php 7+";

pyv="$(php -V 2>&1)";
pyv="${pyv:7:1}";
if (( $pyv < 7)); then
    echo "no php 7+ installed, attempting install now";
    if $OS == 'OSX'; then
    echo "install php here: https://www.python.org/ftp/python/3.6.2/python-3.6.2-macosx10.6.pkg"
    echo "then run again"
    exit 1
    else echo "install PHP 7+ and run this again";
    exit 1
    fi
else echo "PHP 7+ IS HERE, continuing...";

fi

# check for nginx
echo -e "\n$LIGHTRED[$YELLOW*$LIGHTRED]$WHITE CHECKING FOR NGINX $LIGHTRED[$YELLOW*$LIGHTRED]$NC\n";
if ! which nginx > /dev/null 2>&1; then
    echo "Nginx not installed, attempting install now..."
    if $OS == 'Linux'
        if ! uname -a | grep "Ubuntu" > /dev/null 2>&1; then
            "We don't support your linux flavor, install nginx and rerun."
        else
            echo "Installing nginx via apt-get"
            sudo apt-get install nginx
            "NGINX installed, attempting to move files to sites-available..."
            sudo cp config/example.vhost /etc/nginx/sites-available/fugitive
            sudo ln -s /etc/nginx-sites-available/fugitive /etc/nginx/sites-enabled/
        fi
    fi
else 
    "Found NGINX, attempting to move files to sites-available..."
    sudo cp config/example.vhost /etc/nginx/sites-available/fugitive
    sudo ln -s /etc/nginx-sites-available/fugitive /etc/nginx/sites-enabled/
fi
echo "you need to edit your nginx sites-available/fugitive file to match your hostname and other info"
