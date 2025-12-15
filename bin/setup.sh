#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"

echo "Running project setup in $ROOT"

if [ ! -f .env ]; then
  if [ -f .env.oauth ]; then
    cp .env.oauth .env
    echo "Created .env from .env.oauth"
  else
    echo ".env not found and .env.oauth not found. Please create a .env file and re-run." >&2
    exit 1
  fi
else
  echo ".env already exists; keeping existing file (backed up to .env.bak)"
  cp .env .env.bak || true
fi

# Ensure docker-friendly DB_HOST
if grep -q '^DB_HOST=' .env; then
  sed -i.bak -E 's/^DB_HOST=.*/DB_HOST=db/' .env || true
else
  echo "DB_HOST=db" >> .env
fi

echo "Starting Docker services..."
docker-compose up --build -d

echo "Waiting for MySQL to become available (up to 2 minutes)..."
MAX_TRIES=60
COUNT=0
until docker-compose exec -T db mysql -uroot -proot -e 'SELECT 1' >/dev/null 2>&1; do
  COUNT=$((COUNT+1))
  if [ "$COUNT" -ge "$MAX_TRIES" ]; then
    echo "MySQL did not become ready in time" >&2
    exit 1
  fi
  printf '.'
  sleep 2
done
echo "\nMySQL is ready"

echo "Installing PHP dependencies inside app container..."
docker-compose exec app bash -lc "composer install --no-interaction --prefer-dist"

echo "Running migrations and seeders..."
docker-compose exec app bash -lc "php artisan migrate --force --seed"

echo "Creating storage symlink..."
docker-compose exec app bash -lc "php artisan storage:link || true"

echo "Setup complete. Visit http://127.0.0.1 (or https://127.0.0.1) to view the app."
#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"

echo "Running project setup in $ROOT"

if [ ! -f .env ]; then
  if [ -f .env.oauth ]; then
    cp .env.oauth .env
    echo "Created .env from .env.oauth"
  else
    echo ".env not found and .env.oauth not found. Please create a .env file and re-run." >&2
    exit 1
  fi
else
  echo ".env already exists; keeping existing file (backed up to .env.bak)"
  cp .env .env.bak || true
fi

# Ensure docker-friendly DB_HOST
if grep -q '^DB_HOST=' .env; then
  sed -i.bak -E 's/^DB_HOST=.*/DB_HOST=db/' .env || true
else
  echo "DB_HOST=db" >> .env
fi

echo "Starting Docker services..."
docker-compose up --build -d

echo "Waiting for MySQL to become available (up to 2 minutes)..."
MAX_TRIES=60
COUNT=0
until docker-compose exec -T db mysql -uroot -proot -e 'SELECT 1' >/dev/null 2>&1; do
  COUNT=$((COUNT+1))
  if [ "$COUNT" -ge "$MAX_TRIES" ]; then
    echo "MySQL did not become ready in time" >&2
    exit 1
  fi
  printf '.'
  sleep 2
done
echo "\nMySQL is ready"

echo "Installing PHP dependencies inside app container..."
docker-compose exec app bash -lc "composer install --no-interaction --prefer-dist"

echo "Running migrations and seeders..."
docker-compose exec app bash -lc "php artisan migrate --force --seed"

echo "Creating storage symlink..."
docker-compose exec app bash -lc "php artisan storage:link || true"

echo "Setup complete. Visit http://127.0.0.1 (or https://127.0.0.1) to view the app."
