<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo html_escape($title); ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/theme.css'); ?>">
</head>

<body class="bg-dashboard vh-100 overflow-hidden d-flex flex-column flex-md-row">

    <!-- Student navigation is a single item, so it is a fixed brand strip
         rather than the offcanvas sidebar the staff area needs. -->
    <div class="sidebar-bg text-white d-flex flex-column flex-shrink-0 justify-content-between p-3"
         style="width: 250px;">

        <a href="<?php echo site_url('student'); ?>" class="text-white text-decoration-none">
            <span class="fs-4 fw-bold">Voting System</span>
            <small class="d-block text-white-50">Student Portal</small>
        </a>

        <ul class="nav nav-pills flex-column">
            <li class="nav-item">
                <a href="<?php echo site_url('student'); ?>"
                   class="nav-link<?php echo $active === 'home' ? ' active' : ''; ?>">
                    <i class="bi bi-house"></i> My Elections
                </a>
            </li>
        </ul>

        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle"
               id="studentUserDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle me-2"></i>
                <strong class="text-truncate"><?php echo html_escape($user_name ?: $this->session->userdata('email')); ?></strong>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="studentUserDropdown">
                <li>
                    <span class="dropdown-item-text small text-muted">
                        <?php echo html_escape($this->session->userdata('department')); ?>
                    </span>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item" href="<?php echo site_url('auth/change_password'); ?>">
                        <i class="bi bi-key"></i> Change Password
                    </a>
                </li>
                <li>
                    <a class="dropdown-item text-danger fw-bold" href="<?php echo site_url('auth/logout'); ?>">
                        <i class="bi bi-box-arrow-right"></i> Sign out
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="w-100 d-flex flex-column overflow-auto">

        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom px-3 px-md-4 shadow-sm topbar">
            <div class="container-fluid p-0">
                <span class="navbar-brand mb-0 h1 fw-bold fs-5 fs-md-4 text-truncate"><?php echo html_escape($page_heading ?? $title); ?></span>
            </div>
        </nav>

        <main class="container-fluid p-3 p-md-4">
