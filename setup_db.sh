#!/bin/bash
service mariadb start
mariadb -e "CREATE DATABASE IF NOT EXISTS masjid_bgc CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mariadb -e "CREATE USER IF NOT EXISTS 'masjid_user'@'localhost' IDENTIFIED BY 'masjid_password_2024';"
mariadb -e "GRANT ALL PRIVILEGES ON masjid_bgc.* TO 'masjid_user'@'localhost';"
mariadb -e "FLUSH PRIVILEGES;"
echo "DB SETUP DONE"
