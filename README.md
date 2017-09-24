![fugitive logo](https://user-images.githubusercontent.com/616585/30786714-76a78606-a148-11e7-8879-f9fb9284bf40.png)
# fugitive
a secure ephemoral (self-deleting) web chat application written in php with minimal dependancies

## quickstart   
_note: not fully working yet, only user info is displayed and stored as session, welcome to fork and merge request to add messaging into the build-in sqlite db or redis expiring hashes (planned soon)_   
1. Ensure you have PHP installed and Predis and Redis
2. clone this repo then cd to `public` and then `php -S localhost:8000` and visit the address in your browser. You should see a new username generated and a session started. You will get errors if Redis is not running at default without password set on localhost redis instance. Set password accordingly in index.php (separate config yaml coming soon).

## install guide OSX

1. install homebrew (https://brew.sh)
2. `brew install php72 --with-pear`
3. `brew install redis-server`
4. `brew install php72-redis`

## style guide   
Logo: link: `https://user-images.githubusercontent.com/616585/30786714-76a78606-a148-11e7-8879-f9fb9284bf40.png`   
font: Josefin Sans     

