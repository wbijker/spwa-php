# spwa-php
A Server-Powered Web Applications (SPWA) framework using PHP. 

# setup

```bash
docker compose up

# or to rebuild
docker compose build

# refresh composer autoload
# or restart the container. Compose install runs on startup
docker exec -it php7.4.33 composer dump-autoload
```

