web: bash -lc 'echo "PORT=$PORT"; php -S 0.0.0.0:$PORT -t public'

postdeploy: |
  mkdir -p public/uploads
  chmod -R 777 public/uploads || true

  php artisan route:clear || true
  php artisan config:clear || true
  php artisan view:clear || true
  php artisan cache:clear || true
  php artisan optimize:clear || true
  php artisan config:cache || true
