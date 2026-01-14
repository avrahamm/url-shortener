## Building the app with Docker and Sail
<p>
The assumptions are that Git, Docker, sudo are installed.
And Docker is running.
</p>
<p> 
Review the .env.example file to adapt to your values.
</p>

# Steps to build and run the app from terminal.

```bash
mkdir check1
cd check1
git init
git pull https://github.com/avrahamm/url-shortener.git
git branch -M main
#Review the .env.example file and adapt.  
cp .env.example .env
#Install the dependencies inside the container
docker run --rm \
    --pull=always \
    -v "$(pwd)":/opt \
    -w /opt \
    laravelsail/php84-composer:latest \
    bash -c "composer install"

# Assure the user has write permissions
sudo chown -R $USER: .

#Run the app
./vendor/bin/sail up -d

#Generate the app key
./vendor/bin/sail artisan key:generate

#Run the migrations and seed the database
./vendor/bin/sail artisan migrate --seed

# Open a new tab to run the Queue worker.
# So the Queue LogHit job can run asynchronously.
./vendor/bin/sail artisan queue:work

# optionally open another tab and connect to the database to inspect it.
./vendor/bin/sail exec url-shortener-pgsql psql -U sail -d url-shortener-pgsql
url-shortener-pgsql=# select * from links;
url-shortener-pgsql=# \q
```

```bash
# Review the routes/api.php routes/web.php
# And the respective code.
# Experiment with curl calls from terminal.
# You can do it either from inside the container
# or from the host.
# I will show you how to do it from the inside the container.
# Example curl calls exist in each LinkController method comment also.
# Also, I use secret123 as API_KEY.
# You will need to adapt it to your own.
./vendor/bin/sail exec url-shortener bash

curl -X POST http://url-shortener/api/links \
       -H "Content-Type: application/json" \
       -H "Accept: application/json" \
       -H "X-Api-Key: secret123" \
       -d '{
       "target_url": "https://example.com"
       }'
       
curl http://url-shortener/r/apple  \
        -H "Content-Type: application/json"
 
# Now it will take some time to calculate.      
curl http://url-shortener/api/links/apple/stats \
         -H "Content-Type: application/json" \
         -H "Accept: application/json" \
         -H "X-Api-Key: secret123"

# Now it will return fast from Cache.   
curl http://url-shortener/api/links/apple/stats \
         -H "Content-Type: application/json" \
         -H "Accept: application/json" \
         -H "X-Api-Key: secret123"

# This call disables Cache
curl http://url-shortener/r/apple  \
        -H "Content-Type: application/json"
        
# Now it will again take some time to calculate.      
curl http://url-shortener/api/links/apple/stats \
         -H "Content-Type: application/json" \
         -H "Accept: application/json" \
         -H "X-Api-Key: secret123"
         
# You can review the Queue tab to monitor the Queue worker.
# You can monitor the database tables content also.
exit
# Review tests, there are also API calls similar to curl examples above.
# Run tests.

./vendor/bin/sail artisan test

```

## Scaffolding starter with the bash script uses Docker sail
<p>
Just to note about how I created the starter Laravel 10 template.
</p>
<p>
When my-docs/sail-with-params-and-version.sh is 
in the parent directory.
</p>

```bash
chmod +x sail-with-params-and-version.sh
./sail-with-params-and-version.sh
./vendor/bin/sail up -d
```
That's how I created the starter Laravel 10 template.


