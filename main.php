<?php
require_once("session.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/app.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"
        integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>

<body>
    <div class="overlay"></div>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar" class="bg-dark text-white">
            <div class="p-4">
                <div class="logo-container">
                    <img src="/assets/images/logo.png" alt="Logo" class="logo-full">
                    <img src="/assets/images/logo-icon.png" alt="Logo" class="logo-icon">
                </div>
                <hr>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="?page=home">
                            <i class="bi bi-speedometer2"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="?page=report">
                            <i class="bi bi-person"></i>
                            <span>Report</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="?page=setting">
                            <i class="bi bi-gear"></i>
                            <span>Settings</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="/logout.php">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Logout</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-dark d-lg-none">
                        <i class="bi bi-list"></i>
                    </button>
                    <button type="button" id="sidebarCollapseDesktop" class="btn btn-dark d-none d-lg-block">
                        <i class="bi bi-list"></i>
                    </button>
                    <span class="navbar-brand mb-0 h1">Flexiview</span>
                </div>
            </nav>

            <?php

            if (isset($_GET['page'])) {
                $page = $_GET['page'];

                // Sanitize the input to prevent directory traversal attacks
                $page = basename($page);

                // Define the path
                $file_path = 'views/pages/' . $page . '.php';

                // Check if the file exists
                if (file_exists($file_path)) {
                    require_once($file_path);
                } else {
                    // Display a 404 error or redirect to an error page if the file does not exist
                    // Optionally, include a 404 page
                    require_once('views/pages/error/404.php');
                }
            } else {
                require_once('views/pages/home.php');
            }

            ?>
        </div>
    </div>

    <script src="/assets/js/bootstrap.min.js"></script>
    <script src="/assets/js/app.js"></script>

</body>

</html>