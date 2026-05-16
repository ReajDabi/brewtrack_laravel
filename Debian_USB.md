permision:

sudo chmod 666 /dev/usb/lp1
php artisan serve --host=0.0.0.0 --port=8000
cloudflared tunnel --url http://127.0.0.1:8000
change &#8369; to pesos that printer can read

https://service-bulgur-simplify.ngrok-free.dev