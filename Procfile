web: bash -lc 'echo "PORT=$PORT"; php -S 0.0.0.0:$PORT -t public'

postdeploy: |
  mkdir -p public/uploads
  mkdir -p storage/app/public/profile-photos
  chmod -R 777 storage bootstrap/cache public/uploads || true

  php artisan storage:link || true

  php artisan route:clear   || true
  php artisan config:clear  || true
  php artisan view:clear    || true
  php artisan cache:clear   || true
  php artisan optimize:clear|| true
  php artisan config:cache  || true
