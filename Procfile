web: bash -lc 'echo "PORT=$PORT"; php -S 0.0.0.0:$PORT -t public'

postdeploy: |
  php artisan storage:link || true
  php artisan config:clear
  php artisan config:cache
