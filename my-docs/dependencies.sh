#!/bin/bash
# This script uses a temporary Docker image
# to "composer install" the Laravel application dependencies.
# Then, Docker Sail runs the app containers.
# Review and adapt .env.example env variables as needed.
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
    bash -c "composer install"

sudo chown -R $USER: .

