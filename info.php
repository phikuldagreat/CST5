<?php
echo PHP_VERSION;
echo "<br>";
echo extension_loaded('pdo_mysql') ? 'pdo_mysql loaded' : 'pdo_mysql NOT loaded';
echo "<br>";
echo extension_loaded('mysqli') ? 'mysqli loaded' : 'mysqli NOT loaded';