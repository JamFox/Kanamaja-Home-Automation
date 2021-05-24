Technologies used
-----------------

**OS:** [Raspberry Pi OS Lite (headless)](https://www.raspberrypi.org/software/operating-systems/)

**Webserver:** Nginx

**Serverside:** Flask

**WSGI:** Gunicorn

**Database engine:** MariaDB

**CSS and JS:** [Bootstrap 5 beta2's](https://getbootstrap.com) [darkmode fork](https://vinorodrigues.github.io/bootstrap-dark/)

Setup
-----

### Webserver setup with Nginx

Install Nginx with `sudo apt install nginx` and start the Nignx service with `sudo /etc/init.d/nginx start`

Navigate to `sudo nano /etc/nginx/nginx.conf`

[From [e-tinkers](https://www.e-tinkers.com/2018/08/how-to-properly-host-flask-application-with-nginx-and-guincorn/)] Make the following changes for improving potential performance:  
1) Uncommented the **multi_accept** directive and set to **on**, which informs each worker_process to accept all new connections at a time, opposed to accepting one new connection at a time.

2) Change the **keepalive_timeout** from default **65** to **30**. The keepalive\_timeout determines how many seconds a connection to the client should be kept open before it’s closed by Nginx. This directive should be lowered so that idle connections can be closed earlier at 30 seconds instead of 65 seconds.

3) Uncomment the **server_tokens** directive and ensure it is set to **off**. This will disable emitting the Nginx version number in error messages and response headers.

4) Uncomment the **gzip_vary on**, this tell proxies to cache both the gzipped and regular version of a resource where a non-gzip capable client would not display gibberish due to the gzipped files.

5) Uncomment the **gzip_proxied** directive and set it to **any**, which will ensure all proxied request responses are gzipped.

6) Uncomment the **gzip_comp_level** and change the value to **5**. Level provide approximate 75% reduction in any ASCII type of files to achieve almost same result as level 9 but not have significant impact on CPU usage as level 9.

7) Uncomment **gzip_http_version 1.1;**, this will enable compression both for HTTP/1.0 and HTTP/1.1.

8) Add a line **gzip_min_length 256;** right before **gzip_types** directive, this will ensure that the file smiler than 256 bytes would not be gzipped, the default value was set at 20 bytes which is too small and could cause the gzipped file even bigger due to the overhead.

9) Replace the **gzip_types** directive with the following more comprehensive list of MIME types. This will ensure that JavaScript, CSS and even SVG file types are gzipped in addition to the HTML file type.

```bash
gzip_types
	application/atom+xml 
	application/javascript 
	application/json 
	application/rss+xml 
	application/vnd.ms-fontobject 
	application/x-font-ttf 
	application/x-web-app-manifest+json 
	application/xhtml+xml 
	application/xml 
	font/opentype 
	image/svg+xml 
	image/x-icon 
	text/css 
	text/plain 
	text/x-component 
	text/javascript 
	text/xml;
```

Create new config with `sudo nano /etc/nginx/sites-available/app` and add the following:

```bash
server {
listen 80 default_server;
listen [::]:80;

root /var/www/html;

server_name kanamaja.ru; #add to network hosts file with rpis ip like so 192.168.1.109 ruberg.ru

location /static {
alias /var/www/html/static;
}

location / {
try_files $uri @wsgi;
}

location @wsgi {
proxy_pass http://unix:/tmp/gunicorn.sock;
include proxy_params;
}

location ~* .(ogg|ogv|svg|svgz|eot|otf|woff|mp4|ttf|css|rss|atom|js|jpg|jpeg|gif|png|ico|zip|tgz|gz|rar|bz2|doc|xls|exe|ppt|tar|mid|midi|wav|bmp|rtf)$ {
access_log off;
log_not_found off;
expires max;
}
}
```

The location @wsgi block proxies HTTP requests to the `/tmp/gunicorn.sock` unix socket where Flask app will listen to, it also setup some common HTTP headers defined inside the `/etc/nginx/proxy_params` file.


Then remove default config and create a new symbolic link: 

```bash
cd /etc/nginx/sites-enabled/
sudo rm default
sudo ln -s /etc/nginx/sites-available/app .
```

After making changes to the Nginx configuration, restart the service with: `sudo systemctl restart nginx`

### Database setup

Install MariaDB (MySQL fork) with `sudo apt install mariadb-server mariadb-client` and enable it to start on boot with `sudo systemctl enable mariadb.service`

Secure the database with `sudo mysql_secure_installation`

Then enter MariaDB with `sudo mysql` and create a new database with the name "auth" with `CREATE DATABASE auth;` and enter enter the database to modify it with `USE auth;`

Create table for account info:
```bash
CREATE TABLE accounts (
	id INT NOT NULL AUTO_INCREMENT,
  	username VARCHAR(50) NOT NULL,
  	password VARCHAR(255) NOT NULL,
    PRIMARY KEY (id)
) DEFAULT CHARSET=utf8;
```
Then enter MariaDB with `sudo mysql` and create a new user (by default, new users don't have any permissions) for authentication purposes with `CREATE USER 'ruberg'@'localhost' IDENTIFIED BY 'ruberg';`

"ruberg" is used as a template here. If you want to create the user with another name and password you must also change them accordingly in 'app.py'

Add read only permissions to new user with `GRANT SELECT ON auth.accounts TO 'ruberg'@'localhost';` and refresh privileges with `FLUSH PRIVILEGES;`

To add new users to the database use `INSERT INTO accounts (username, password) VALUES ('ruberg', '$2y$10$.0y6NZBfztbXm362M1dAne1HT41XK4teakMj.1zWvVJDjzxoR4m2y');` 

NOTE: only use hashed passwords from /register, current setup compares hashes, plain passwords DO NOT WORK!

### Flask setup (with Gunicorn)

`sudo apt-get install python3-pip python3-mysql.connector` and `sudo pip3 install flask` and `flask-sqlalchemy flask-login passlib`

`sudo pip3 install gunicorn` and try gunicorn manually in `/var/html/www` with `gunicorn --bind=unix:/tmp/gunicorn.sock --workers=4 app:app` (NOTE: `--workers=4` refers to using 4 cores here, set according to your system!)

To run a Gunicorn service upon boot `sudo nano /etc/systemd/system/gunicorn.service`

Paste in the configuration and change accordingly:

```bash
[Unit]
Description=gunicorn daemon for /var/www/html/app.py
After=network.target

[Service]
User=pi
Group=www-data
RuntimeDirectory=gunicorn
WorkingDirectory=/var/www/html/
ExecStart=/usr/local/bin/gunicorn --bind=unix:/tmp/gunicorn.sock --workers=4 app:app
ExecReload=/bin/kill -s HUP $MAINPID
ExecStop=/bin/kill -s TERM $MAINPID

[Install]
WantedBy=multi-user.target
```

`sudo systemctl enable gunicorn` and `sudo systemctl start gunicorn` and to test `sudo systemctl status gunicorn`

If you make changes to the `gunicorn.service` or `app.py` file, reload the daemon service definition and restart the Gunicorn process by typing:

`sudo systemctl daemon-reload` and `sudo systemctl restart gunicorn`

Usage
-----

### Get the files with Git


To clone the contents directly without the source folder use: `git init` `git remote add origin git@github.com:me/name.git` `git pull origin master`
If that gives errors that some txt files are missing than just delete the /www directory and clone the project and move the www into /var.
### Nginx usage

Start the Nignx service with `sudo systemctl start nginx`

Stop the Nignx service with `sudo systemctl stop nginx`

Whenever you make changes to the Nginx configuration, you need to restart or reload the webserver processes. Execute the following command to restart the Nginx service: `sudo systemctl restart nginx`

### Database usage

Current authentication uses hashed password compare function, plain passwords DO NOT WORK!. To add new users to the database use: `INSERT INTO accounts (username, password) VALUES ('ruberg', '$2y$10$.0y6NZBfztbXm362M1dAne1HT41XK4teakMj.1zWvVJDjzxoR4m2y');`
 
 To generate hashed passwords use '/register', also accessible from the index/login page.

Table for users uses the following structure:

```bash
accounts (
	id INT NOT NULL AUTO_INCREMENT,
  	username VARCHAR(50) NOT NULL,
  	password VARCHAR(255) NOT NULL,
    PRIMARY KEY (id)
) DEFAULT CHARSET=utf8;
```
### Needed packages

If you re having issues with imports, `sudo pip install passlib`

download Astral `sudo pip3 install astral==2.2`

Keep in mind that this code in it's unaltered state works only with the +2/+3 UTC timezone (Tallinn)

Info about astral: https://astral.readthedocs.io/en/latest/#sun

To make the right scripts run on startup type `sudo nano /etc/rc.local`
and add these at the end before exit 0
`sudo python3 /var/www/html/app.py &`
`sudo python3 /var/www/html/relee.py &`
`sudo python3 /var/www/html/automaticOnStartUp.py &`
`sudo python3 /var/www/html/temp.py &`
`sudo python3 /var/www/html/lcd.py &`

ctr+x and type y to save.
Dont forget the & at the end or you'll brick your pi. :)
For LCD download `sudo pip3 install Adafruit-CharLCD`

remove admin-only permissions with:
`sudo chmod 777  /var/www/html -R`


