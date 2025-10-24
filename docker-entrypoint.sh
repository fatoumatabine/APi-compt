#!/bin/sh

# Configurer Apache pour le port dynamique
echo "Configuring Apache for port $PORT"
sed -i "s/\${PORT}/$PORT/g" /etc/apache2/ports.conf
sed -i "s/\${PORT}/$PORT/g" /etc/apache2/sites-available/000-default.conf
echo "Ports configured"

# Attendre que la base de données soit prête
echo "Waiting for database to be ready..."
if [ -n "$DATABASE_URL" ]; then
# Extraire l'hôte et le port de DATABASE_URL (format: postgres://user:pass@host:port/db)
DB_HOST=$(echo $DATABASE_URL | sed 's|postgres://[^@]*@\([^:]*\):\([^/]*\)/.*|\1|')
DB_PORT=$(echo $DATABASE_URL | sed 's|postgres://[^@]*@\([^:]*\):\([^/]*\)/.*|\2|')
fi
TIMEOUT=300  # 5 minutes
COUNTER=0
HOST=${DB_HOST:-${PGHOST:-127.0.0.1}}
PORT=${DB_PORT:-${PGPORT:-5432}}
while ! pg_isready -h $HOST -p $PORT; do
if [ $COUNTER -ge $TIMEOUT ]; then
  echo "Database connection timeout reached. Proceeding without waiting."
  break
fi
echo "$HOST:$PORT - no response"
echo "-p:$PORT - no response"
  echo "Database is unavailable - sleeping"
  sleep 1
  COUNTER=$((COUNTER + 1))
done

echo "Database is up - executing migrations"
php artisan migrate --force

echo "Starting Apache..."
exec "$@"
