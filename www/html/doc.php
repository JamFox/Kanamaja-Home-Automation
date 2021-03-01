<?php
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
    header('Location: 401.html');
    exit;
}
?>

<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.80.0">
    <!-- The page supports both dark and light color schemes,
         and the page author prefers / default is dark -->
    <meta name="color-scheme" content="light dark">
    <!-- Bootstrap -->
    <link href="css/bootstrap-nightshade.min.css" rel="stylesheet">
    <!-- Custom styles -->
    <link href="css/dashboard.css" rel="stylesheet">
    <!-- Page specific -->
    <title>KASS Documentation</title>
    <link rel="icon" href="images/rubergkawai.png" type="image/png">
</head>

<body>
    <header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow" style="background-color: #e3f2fd;">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="dashboard.php">KASS</a>
        <a id="darkmode-button" class="mt-1 mb-1 btn btn-dark active ms-auto">Dark mode<i class="d-none d-light-inline" title="Toggle dark mode"></i></a>
        <a class="ms-2 me-2 btn btn-dark active" href="logout.php">Sign out</a>
        <button class="navbar-toggler d-md-none collapsed ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    </header>

    <div class="container-fluid">
        <div class="row">
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse" style="top: 0px;">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="dashboard.php">
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="#top">
                                Documentation
                            </a>
                        </li>
                        <li class="ms-4 nav-item">
                            <a class="nav-link text-secondary" href="#tech">
                                Technologies used
                            </a>
                        </li>
                        <li class="ms-4 nav-item">
                            <a class="nav-link text-secondary" href="#setup">
                                Setup
                            </a>
                        </li>
                        <li class="ms-4 nav-item">
                            <a class="nav-link text-secondary" href="#usage">
                                Usage
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h1"><a name="top">KASS documentation</a></h1>
                </div>


                <h2 class="h3"><a name="tech">Technologies used</a></h2>
                <p><b>OS:</b> <a href="https://www.raspberrypi.org/software/operating-systems/">Raspberry Pi OS Lite (headless)</a></p>
                <p><b>Webserver:</b> Nginx</p>
                <p><b>PHP:</b> PHP7.3</p>
                <p><b>Database engine:</b> MariaDB</p>
                <p><b>CSS and JS:</b> <a href="https://getbootstrap.com">Bootstrap 5 beta2's</a> <a href="https://vinorodrigues.github.io/bootstrap-dark/">darkmode fork</a></p>


                <h2 class="h3"><a name="setup">Setup</a></h2>

                <h3 class="h5">Webserver setup with PHP</h3>
                <p>Install Nginx with <code>sudo apt install nginx</code> and start the Nignx service with <code>sudo /etc/init.d/nginx start</code></p>
                <p>Install PHP with <code>sudo apt install php7.3 php7.3-common php7.3-cli php7.3-fpm php7.3-mysql</code></p>
                <p>Navigate to <code>cd /etc/nginx</code> and open Nginx default configuration with <code>sudo nano sites-enabled/default</code></p>
                <p>Find and make sure the following lines are uncommented:
                <pre><code>location ~ \.php$ {
    include snippets/fastcgi-php.conf;
    fastcgi_pass unix:/var/run/php5-fpm.sock;
}
</code></pre>
                </p>
                <p>After making changes to the Nginx configuration, restart the service with: <code>sudo systemctl restart nginx</code></p>

                <h3 class="h5">Get the files with Git</h3>
                <p>Navigate to <code>cd /var/</code> and clone the repository with <code>git clone</code></p>

                <h3 class="h5">Database setup</h3>
                <p>Install MariaDB (MySQL fork) with <code>sudo apt install mariadb-server mariadb-client</code> and enable it to start on boot with <code>sudo systemctl enable mariadb.service</code></p>
                <p>Secure the database with <code>sudo mysql_secure_installation</code></p>
                <p>Then enter MariaDB with <code>sudo mysql</code> and create a new database with the name "auth" with <code>CREATE DATABASE auth;</code> and enter enter the database to modify it with <code>USE auth;</code></p>
                <p>Create table for account info:
                <pre><code>CREATE TABLE accounts (
	id INT NOT NULL AUTO_INCREMENT,
  	username VARCHAR(50) NOT NULL,
  	password VARCHAR(255) NOT NULL,
    PRIMARY KEY (id)
) DEFAULT CHARSET=utf8;</code></pre>
                </p>
                <p>Then enter MariaDB with <code>sudo mysql</code> and create a new user (by default, new users don't have any permissions) for authentication purposes with <code>CREATE USER 'ruberg'@'localhost' IDENTIFIED BY 'ruberg';</code> (ruberg is used as a template here)</p>
                <p>Add read only permissions to new user with <code>GRANT SELECT ON auth.accounts TO 'ruberg'@'localhost';</code> and refresh privileges with <code>FLUSH PRIVILEGES;</code></p>
                <p>Current PHP setup uses password hashes to verify passwords. To add new users to the database use <code>INSERT INTO accounts (id, username, password) VALUES (1, 'ruberg', '$2y$10$.0y6NZBfztbXm362M1dAne1HT41XK4teakMj.1zWvVJDjzxoR4m2y');</code> (NOTE: inserting plain passwords will not work with the current PHP setup, as PHP compares hashes)</p>
                <p>To generate hashed passwords use <code>www/html/reg.php</code>, also accessible from the webserver's index page.</p>


                <h2 class="h3"><a name="usage">Usage</a></h2>

                <h3 class="h5">Nginx usage</h3>
                <p>Start the Nignx service with <code>sudo systemctl start nginx</code></p>
                <p>Stop the Nignx service with <code>sudo systemctl stop nginx</code></p>
                <p>Whenever you make changes to the Nginx configuration, you need to restart or reload the webserver processes. Execute the following command to restart the Nginx service: <code>sudo systemctl restart nginx</code></p>

                <h3 class="h5">Database usage</h3>
                <p>Current PHP setup uses password hashes to verify passwords. To add new users to the database use <code>INSERT INTO accounts (id, username, password) VALUES (1, 'ruberg', '$2y$10$.0y6NZBfztbXm362M1dAne1HT41XK4teakMj.1zWvVJDjzxoR4m2y');</code> (NOTE: inserting plain passwords will not work with the current PHP setup, as PHP compares hashes)</p>
                <p>To generate hashed passwords use <code>www/html/reg.php</code>, also accessible from the webserver's index page.</p>

                <p>Table for users uses the following structure:
                <pre><code>accounts (
	id INT NOT NULL AUTO_INCREMENT,
  	username VARCHAR(50) NOT NULL,
  	password VARCHAR(255) NOT NULL,
    PRIMARY KEY (id)
) DEFAULT CHARSET=utf8;</code></pre>
                </p>
            </main>
        </div>
    </div>
    <!-- Bootstrap Bundle with Popper -->
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
    <!-- Darkmode scripts -->
    <script src="js/darkmode.min.js"></script>
    <script>
        document.querySelector("#darkmode-button").onclick = function(e) {
            darkmode.toggleDarkMode();
        }
        document.querySelector("#darkmode-off-button").onclick = function(e) {
            darkmode.setDarkMode(false); // save=true is default
        }
        document.querySelector("#darkmode-on-button").onclick = function(e) {
            darkmode.setDarkMode(true); // save=true is default
        }
        document.querySelector("#darkmode-forget").onclick = function(e) {
            darkmode.resetDarkMode();
        }
    </script>
</body>

</html>