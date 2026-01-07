#!/bin/bash
#content of https://laravel.build/short3?with=pgsql,redis
# This script scaffolds a new Laravel application
# using Laravel Sail services specified in url "with" query parameter.
# I adapted to create Laravel 10 starter and not latest.
docker info > /dev/null 2>&1

# Ensure that Docker is running...
if [ $? -ne 0 ]; then
    echo "Docker is not running."

    exit 1
fi

docker run --rm \
    --pull=always \
    -v "$(pwd)":/opt \
    -w /opt \
    laravelsail/php84-composer:latest \
    bash -c "composer create-project laravel/laravel="^10.0" short3 --no-interaction && cd short3 && php ./artisan sail:install --with=pgsql,redis "

cd short3

# Allow build with no additional services..
if [ "pgsql redis" == "none" ]; then
    ./vendor/bin/sail build
else
    ./vendor/bin/sail pull pgsql redis
    ./vendor/bin/sail build
fi

CYAN='\033[0;36m'
LIGHT_CYAN='\033[1;36m'
BOLD='\033[1m'
NC='\033[0m'

echo ""

if command -v doas &>/dev/null; then
    SUDO="doas"
elif command -v sudo &>/dev/null; then
    SUDO="sudo"
else
    echo "Neither sudo nor doas is available. Exiting."
    exit 1
fi

if $SUDO -n true 2>/dev/null; then
    $SUDO chown -R $USER: .
    echo -e "${BOLD}Get started with:${NC} cd short3 && ./vendor/bin/sail up"
else
    echo -e "${BOLD}Please provide your password so we can make some final adjustments to your application's permissions.${NC}"
    echo ""
    $SUDO chown -R $USER: .
    echo ""
    echo -e "${BOLD}Thank you! We hope you build something incredible. Dive in with:${NC} cd short3 && ./vendor/bin/sail up"
fi
