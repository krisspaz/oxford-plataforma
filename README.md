## Acerca de Plataforms

# Enlace Simbolico Storage

<p align="center">En caso de no visualizar documentos almacenados en el Storage eliminar el enlace simbolico con el comando "rm public/storage" ya que el cambio de directorios raiz crea conflictos en ocasiones </p>

<p align="center">Posterior a la eliminacion vuelvalo a crear un enalce simbolico nuevo con el comando "php artisan storage:link" </p>

<p align="center">
Permiso en Apache ojo despues de /html/"laravel" en vez de laravel es el nombre de la carpeta del proyecto </p>

<p align="center">
sudo chown -R www-data:www-data /var/www/html/laravel </p>

<p align="center">
sudo chmod -R 775 /var/www/html/laravel </p>

<p align="center">
sudo chmod -R 775 /var/www/html/laravel/storage </p>

<p align="center">
sudo chmod -R 775 /var/www/html/laravel/bootstrap/cache
</p>

# Version de PHP

<p align="center">La Version de PHP 7.4.33-Win32-vc15-x64 multi hilo seguro "vc" </p>
<p align="center">Debe ser multi hilo para generar procesos en paralelo </p>

<p align="center">
Instalacion en Linux:
sudo apt update
sudo apt upgrade
</p>
<p align="center">
Instalar dependencias:
sudo apt install -y software-properties-common
</p>
<p align="center">
Agregar Repoitorio
sudo add-apt-repository ppa:ondrej/php
sudo apt update
</p>
<p align="center">
Instalar PHP 7.4.33 Thread Safe (TS):
sudo apt install php7.4-cli php7.4-fpm php7.4-common php7.4-mysql php7.4-xml php7.4-mbstring php7.4-curl php7.4-json php7.4-opcache php7.4-zip php7.4-intl php7.4-imagick
</p>
<p align="center">
Verificar version:
php -v
</p>
<p align="center">
Configuración Servidor Apache:
sudo a2enmod php7.4
sudo systemctl restart apache2
</p>
<p align="center">
Configuración en Nginx con PHP-FPM:

sudo systemctl enable php7.4-fpm
sudo systemctl start php7.4-fpm
sudo systemctl status php7.4-fpm

</p>

# Libreria Imagemaick "DLL"

<p align="center">Version 7.1.0-57-Q16-HDRI </p>
<p align="center">Esta libreria nos permite generar codigo QR </p>

# Libreria Imagemaick "DLL" instalar en linux

<p align="center">wget https://imagemagick.org/download/releases/ImageMagick-7.1.0-57.tar.xz </p>
<p align="center">
Extraer y Compilar
tar -xf ImageMagick-7.1.0-57.tar.xz
cd ImageMagick-7.1.0-57
./configure
make
sudo make install
</p>
<p align="center">
Verificar si la instalacion fue exitosa
convert --version
</p>
<p align="center">
Reecompila Instalacion
sudo apt-get install php-dev gcc make autoconf
</p>
<p align="center">
Recompilar e instalar la extensión
sudo pecl install
</p>
<p align="center">
Añadir la extensión al archivo php.ini:
extension=imagick.so
</p>

# Seeders Importantes

<p align="center">
Los Seeders Implementados Restablece las variables de configuración por defecto en:
-Variables Globales
php artisan db:seed --class=CmsSettingsSeeder
-Menu
php artisan db:seed --class=CmsMenusSeeder
-Privilegio de Menu
php artisan db:seed --class=CmsMenusPrivilegesSeeder
-Modulos del Backend
php artisan db:seed --class=CmsModulsSeeder
-Privilegios
php artisan db:seed --class=CmsPrivilegesSeeder
-Privilegios y Roles
php artisan db:seed --class=CmsPrivilegesRolesSeeder

Si se elimino algun Menu serviran los seders de CmsMenusSeeder y CmsMenusPrivilegesSeeder para restablecerlos



</p>

# Diagrama E-R

<p align="center"><a href="#" target="_blank"><img src="https://i.imgur.com/FrsSNi2.png" width="400"></a></p>

# Login

<p align="center"><a href="#" target="_blank"><img src="https://i.imgur.com/NUPXo3c.png" width="400"></a></p>

# Acceso Admin

<p align="center"><a href="#" target="_blank"><img src="https://i.imgur.com/O6xJ4ln.png" width="400"></a></p>

# Menu Super Admin

<p align="center"><a href="#" target="_blank"><img src="https://i.imgur.com/fjQ8nuQ.png" width="401"></a></p>
