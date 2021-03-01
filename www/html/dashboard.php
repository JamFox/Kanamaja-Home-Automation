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
    <title>KASS Dashboard</title>
    <link rel="icon" href="images/rubergkawai.png" type="image/png">
</head>

<body>
    <header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow" style="background-color: #e3f2fd;">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="#">KASS</a>
        <a id="darkmode-button" class="mt-1 mb-1 btn btn-dark active ms-auto">Dark mode<i class="d-none d-light-inline" title="Toggle dark mode"></i></a>
        <a class="ms-2 me-2 btn btn-dark active" href="logout.php">Sign out</a>
        <button class="navbar-toggler d-md-none collapsed ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    </header>

    <div class="container-fluid">
        <div class="row">
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse" style="top: 0px;">
                <div class="position-sticky pt-2">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="#top">
                                Dashboard
                            </a>
                        </li>
                        <li class="ms-4 nav-item">
                            <a class="nav-link text-secondary" href="#video">
                                Video feed
                            </a>
                        </li>
                        <li class="ms-4 nav-item">
                            <a class="nav-link text-secondary" href="#controls">
                                Controls
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="doc.php">
                                Documentation
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><a name="top">Welcome, <?= $_SESSION['name']?>!</a></h1>
                </div>

                <h2 class="h3"><a name="video">Main video feed</a></h2>
                <div class="embed-responsive embed-responsive-16by9">
                    <iframe src="https://www.youtube.com/embed/ULeDlxa3gyc" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>

                <h2 class="h3"><a name="controls">Controls</a></h2>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-5 me-1 mb-1 mt-1">
                            <div class="card h-100">
                                <div class="card-header">
                                    Light controls
                                </div>
                                <div class="card-body">
                                    <div class="form-check form-switch form-switch-md">
                                        <input class="form-check-input" type="checkbox" id="flexSwitch1" />
                                        <div>
                                            <label class="form-check-label" style="margin-left:10px;" for="flexSwitchLight">Light override</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-muted">
                                    Lights are:
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5 mb-1 mt-1">
                            <div class="card h-100">
                                <div class="card-header">
                                    Temperature controls
                                </div>
                                <div class="card-body">
                                    <div class="form-check form-switch form-switch-md">
                                        <input class="form-check-input" type="checkbox" id="flexSwitch2" />
                                        <div>
                                            <label class="form-check-label" style="margin-left:10px;" for="flexSwitchRadiator">Radiator override</label>
                                        </div>
                                    </div>
                                    <div class="container height-100 d-flex justify-content-center align-items-center">
                                        <div class="w-100">
                                            <div class="mt-3 d-flex flex-column align-items-center"> <label for="steprange" class="form-label">Temp Slider (20-30C)</label> <input type="range" class="form-range" min="20" max="30" step="1" id="steprange"> </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-muted">
                                    Current temperature: Radiator is:
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-5 me-1 mb-1 mt-1">
                            <div class="card h-100">
                                <div class="card-header">
                                    Video controls
                                </div>
                                <div class="card-body">

                                </div>
                                <div class="card-footer text-muted">
                                    Sample
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5 mb-1 mt-1">
                            <div class="card h-100">
                                <div class="card-header">
                                    More controls
                                </div>
                                <div class="card-body">

                                </div>
                                <div class="card-footer text-muted">
                                    Sample
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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