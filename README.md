# KASS: Kanamaja Advanced Surveillance System

**OS**: [Raspberry Pi OS Lite (headless)] (https://www.raspberrypi.org/software/operating-systems/) 

**Webserver**: [NGINX](https://www.raspberrypi.org/documentation/remote-access/web-server/nginx.md)

**CSS and JS**: [Bootstrap](https://getbootstrap.com) and it's [dark mode fork](https://vinorodrigues.github.io/bootstrap-dark/).

## Setup

[Set up Nginx (with PHP)](https://www.raspberrypi.org/documentation/remote-access/web-server/nginx.md)

After installing Nginx on Raspberry Pi, navigate to var/www/ for webserver assets and resources (the main dir for this repo)

Configure Nginx from it's configuration file at: `etc/nginx/nginx.conf`

Install PHP: `sudo apt install php7.3 php7.3-common php7.3-cli php7.3-fpm php7.3-mysql`

Install MariaDB (better MySQL fork): `sudo apt install mariadb-server mariadb-client`

Enable MariaDB to start on boot with `sudo systemctl enable mariadb.service`

Secure the database with `sudo mysql_secure_installation`

Enter to MariaDB with `sudo mysql` and create the database for accounts with: `CREATE DATABASE auth;` 

Create table for account info:

```bash
CREATE TABLE accounts (
	id INT NOT NULL AUTO_INCREMENT,
  	username VARCHAR(50) NOT NULL,
  	password VARCHAR(255) NOT NULL,
    PRIMARY KEY (id)
) DEFAULT CHARSET=utf8;
```

Create new database user: `CREATE USER 'ruberg'@'localhost' IDENTIFIED BY 'ruberg';`

Add read only permissions to new user `GRANT SELECT ON auth.accounts TO 'ruberg'@'localhost';` and `FLUSH PRIVILEGES;`

To generate password hash for new users use in www/html/reg.php

With SQL root insert new accounts with (use password_hash() hashed passes): ```bash 
INSERT INTO accounts (id, username, password) VALUES (1, 'ruberg', '$2y$10$.0y6NZBfztbXm362M1dAne1HT41XK4teakMj.1zWvVJDjzxoR4m2y');```

