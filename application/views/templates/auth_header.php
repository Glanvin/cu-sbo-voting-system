<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo html_escape($title); ?></title>

    <!-- Bootstrap 5.3.3 and Bootstrap Icons, the versions used across this
         workspace. The legacy views pinned 5.3.0 and pulled the Material
         Symbols icon font from Google Fonts instead. -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/theme.css'); ?>">
</head>

<body class="bg-auth">
    <div class="container d-flex align-items-center justify-content-center py-5" style="min-height: 100vh;">
