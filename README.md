![fugitive logo](https://user-images.githubusercontent.com/616585/30786714-76a78606-a148-11e7-8879-f9fb9284bf40.png)
# fugitive
a secure ephemoral (self-deleting) web chat application written in php with minimal dependancies

## goals

1. run this server on a dark net server (.onion, .i2p, .bit, etc.)
2. no javascript to exploit
3. no logins to exploit
4. no logs / data stored permanently
5. private chat rooms anyone can create with blockchain (base58) hash for room id (yay, we are blockchain bandwagon!)
6. users and chat rooms auto-wiped on inactivity as well as via destroy button
7. communicate securely using out-of-band verification (users can confirm via second form of comms their current session usernames and chat hash id and then 'go secure' much like OTR chat.
8. deploy quickly with minimal / no dependancies on any server including raspberry pi's / other lite hardware.
9. auto-burn messages so you don't have to worry about leaving a trail, because we already have a lot of worries in life!
10. avoid incarceration, coercion, blackmail, capture, etc. for speaking your mind to others. even in a police state, this server will keep you secure to type your mind to another. This will not protect your machine though! remember you could still have keyloggers, etc. backdooring your machine and owning your ass. secure yourself Qubes, Tails, etc. on open source hardware / software using DNScrypt, Privoxy, etc. This is BEYOND the scope of this project. Stay safe out there!

## quickstart   
_note: not fully working yet, only user info is displayed and stored as session, welcome to fork and merge request to add messaging into the build-in sqlite db or redis expiring hashes (planned soon)_   
1. Ensure you have PHP installed and Predis and Redis
2. clone this repo then cd to `public` and then `php -S localhost:8000` and visit the address in your browser. You should see a new username generated and a session started. You will get errors if Redis is not running at default without password set on localhost redis instance. Set password accordingly in index.php (separate config yaml coming soon).

## install guide OSX

1. install homebrew (https://brew.sh)
2. `brew install php72 --with-pear`
3. `brew install redis-server`
4. `brew install php72-redis`
5. `pear channel-discover pear.nrk.io`
6. `pear install nrk/predis`

## install guide Ubuntu 16.04 LTS

1. update apt
2. install redis, php7+, e.g. `sudo apt install -y redis-server php7.0-fpm php7.0-redis php7.0-mcrypt`
3. install nginx `sudo apt install -y nginx`

# install guide Ubuntu 14.04 LTS

1. `sudo add-apt-repository ppa:ondrej/php`
2. `sudo add-apt-repository  
3. `sudo apt-get update && sudo apt-get upgrade`
4. `sudo apt-get install php7.0-fpm redis-server`
5. `sudo apt-get install php7.0-redis php7.0-mcrypt`
6. `sudo apt-get install php7.0-xml`
7. `pear channel-discover pear.nrk.io`
8. `pear install nrk/predis`

# regardless of OS do these after following steps above   
clone repo to a nice folder
copy over nginx-fugitive.example to /etc/nginx/sites-available/fugitive
modify it as necessary `sudo nano /etc/nginx/sites-available/fugitive` change root location and domain name
confirm you have a domain and dns zone file setup to point to your host and then run certbot to enforce https

## style guide   
Logo: link: (https://j1c.co/2y1TQZV)     
font: Josefin Sans     

## default nginx for php-fpm

```
server {
    listen 80;
    
    # ssl stuff to secure your shit
    ssl_protocols TLSv1.2;
    ssl_ciphers 'ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384:ECDHE-ECDSA-CHACHA20-POLY1305:ECDHE-RSA-CHACHA20-POLY1305:ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-SHA384:ECDHE-RSA-AES256-SHA384:ECDHE-ECDSA-AES128-SHA256:ECDHE-RSA-AES128-SHA256';
    ssl_prefer_server_ciphers on;
    ssl_dhparam /etc/ssl/certs/dhparam.pem;
    ssl_stapling on;
    ssl_stapling_verify on;
    resolver 8.8.8.8 8.8.4.4 valid=300s;
    resolver_timeout 5s;
    root /home/[yourusername]/projects/fugitive/public/;
    index index.php index.html index.htm;
    access_log /home/[yourusername]/projects/logs/access.log;
    error_log /home/cjer/[yourusername]/logs/error.log error;
    server_name mycooldomain.chat;

    # header info
    add_header X-Frame-Options DENY;
    add_header Strict-Transport-Security max-age=15768000;
    
    location / {
        try_files $uri $uri/ =404;
    }

    location ~ \.php$ {
        try_files $uri $uri/ @rewrites;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:/var/run/php/php7.0-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    # rewrites
    location @rewrites {
    if ($uri ~* ^/([a-z]+)$) {
        set $page_to_view "/$1.php";
        rewrite ^/([a-z]+)$ /$1.php last;
    }

    location ~ /\.ht {
        deny all;
    }
    listen 443 ssl http2; # managed by Certbot
    ssl_certificate /etc/letsencrypt/live/yourcooldomain.chat/fullchain.pem; # managed by Certbot
    ssl_certificate_key /etc/letsencrypt/live/yourcooldomain.chat/privkey.pem; # managed by Certbot
    include /etc/letsencrypt/options-ssl-nginx.conf; # managed by Certbot
    
    	error_page 502 /502.html;
 location = /502.html {

      root  /home/[yourusername]/projects/errors/;

  }
}
```

## Screenshot of working demo:

![demo screenshot](https://user-images.githubusercontent.com/616585/30787067-02f8f77e-a14f-11e7-914f-6f2fb4fc790d.png)
