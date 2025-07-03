<?php
// Ensure $pageTitle is defined to avoid errors
$pageTitle = isset($pageTitle) ? $pageTitle : "Dashboard"; 

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
       <meta name="description" content="VimaPortal - A comprehensive school management system for academics, student management, exams, attendance, finance, and more." />
        <meta name="author" content="VimaDigitals Development Team" />

        <title><?= htmlspecialchars($_SESSION['user']['type']) ?> | <?= $pageTitle; ?></title>

        <link rel="icon" href="../assets/images/favicon.ico" type="image/x-icon" />

        <!-- Fonts and icons -->
        <script src="../assets/js/plugin/webfont/webfont.min.js"></script>
        <script>
        WebFont.load({
            google: { families: ["Public Sans:300,400,500,600,700"] },
            custom: {
                families: [
                    "Font Awesome 5 Solid",
                    "Font Awesome 5 Regular",
                    "Font Awesome 5 Brands",
                    "simple-line-icons"
                ],
                urls: ["../assets/css/fonts.min.css"]
            },
            active: function () {
                sessionStorage.fonts = true;
            }
        });
        </script>
        <script src='https://meet.jit.si/external_api.js'></script>

        <!-- CSS Files -->
       
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        
        
        <!-- Local Styles -->
        <link href="../summernote/summernote-bs5.min.css" rel="stylesheet">
        <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
        <link rel="stylesheet" href="../assets/css/plugins.min.css" />
        <link rel="stylesheet" href="../assets/css/kaiadmin.min.css" />
        <link rel="stylesheet" href="../assets/css/demo.css" />
        <link rel="stylesheet" href="../assets/css/styles.css" />
        
       


    </head>
    <body>