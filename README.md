# JentExchange — Local Development

This repository contains the JentExchange Laravel application and supporting local-dev files to run inside Docker/WSL.

Quick start (Docker):

1. Copy your environment file and secrets locally (do NOT commit `.env` to git):

```bash
cp app/.env.oauth app/.env
# Edit app/.env to set DB credentials or use the defaults in docker-compose.yml
```

2. Build and start services:

```bash
docker-compose up --build -d
```

3. Visit:
- http://127.0.0.1 for the app (HTTP)
- https://127.0.0.1 for HTTPS (self-signed certs may need acceptance)

Notes:
- The app code is mounted into the container for iterative development. Storage is mounted as a host volume.
- Do not commit `.env`, `*.pem` or other secrets — they are ignored by `.gitignore`.
- If using Windows + WSL, ensure port forwarding (wslrelay or netsh portproxy) is configured so `https://127.0.0.1` in Windows routes to the app in WSL.

App context summary: see `app/ai/ai_context.txt` for a short description and troubleshooting checklist.

If you want I can also add more Dockerfile optimizations, a local Compose override for production-like services, or a CI workflow.

Database dump and initial seed
-----------------------------

- A SQL dump of the current MySQL database is included at `app/docker/mysql/jentexchange_dump.sql`.
- The `docker-compose.yml` mounts `./docker/mysql` into the MySQL container's `/docker-entrypoint-initdb.d` directory. On first start of a fresh volume the MySQL image will automatically import any `*.sql` files placed there.
- To force reinitialize the database from this dump (WARNING: this will erase existing DB data in the `db_data` volume):

```bash
# Stop containers
docker-compose down

# Remove the existing DB volume
docker volume rm $(basename "$PWD")_db_data || true

# Start containers and let MySQL import the dump
docker-compose up --build -d
```

Replace `$(basename "$PWD")_db_data` with the actual volume name if needed (use `docker volume ls` to inspect).

If you prefer migrations instead of an SQL dump, run inside the `app` service after containers are up:

```bash
docker-compose exec app bash -lc "composer install && php artisan migrate --seed && php artisan storage:link"
```

