step1

php artisan tinker

step2
// For Debian USB direct connection
\App\Models\Setting::set('printer_connection', 'linux_usb');
\App\Models\Setting::set('printer_name',       'Xprinter');
\App\Models\Setting::set('auto_print',         '1');
exit

step3
php artisan serve --host=0.0.0.0 --port=8000


Test Print
php artisan tinker

// Quick printer test
$connector = new \Mike42\Escpos\PrintConnectors\FilePrintConnector('/dev/usb/lp0');
$printer   = new \Mike42\Escpos\Printer($connector);
$printer->text("BrewTrack Test Print\n");
$printer->text("Printer is working!\n");
$printer->feed(3);
$printer->cut();
$printer->close();
echo "Printed successfully!";
exit


If this prints → everything is working.
If you get permission error → run sudo chmod 666 /dev/usb/lp0 again.


Make printer permission permanent
The /dev/usb/lp0 permission resets on reboot. Make it permanent:

# Create a udev rule
sudo nano /etc/udev/rules.d/99-xprinter.rules

Paste this inside:
SUBSYSTEM=="usb", ATTRS{idVendor}=="0416", MODE="0666", GROUP="lp"

Save with Ctrl+X → Y → Enter

# Reload udev rules
sudo udevadm control --reload-rules
sudo udevadm trigger

 php artisan serve --host=0.0.0.0 --port=8000

  hostname -I                                 
