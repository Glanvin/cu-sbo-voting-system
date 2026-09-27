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

    <!-- Offcanvas sidebar. Shared by every admin and moderator page, which
         the legacy app handled by repeating this block, plus the topbar,
         in all 12 of its view files. -->
    <div class="sidebar-bg text-white shadow offcanvas-md offcanvas-start d-flex flex-column flex-shrink-0"
         tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel" style="width: 250px;">

        <div class="offcanvas-header d-md-none border-bottom border-secondary">
            <h5 class="offcanvas-title fw-bold" id="sidebarMenuLabel">Voting System</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                    data-bs-target="#sidebarMenu" aria-label="Close"></button>
        </div>

        <div class="offcanvas-body d-flex flex-column flex-grow-1 p-3">
            <a href="<?php echo site_url('dashboard'); ?>"
               class="d-none d-md-flex align-items-center mb-3 me-md-auto text-white text-decoration-none px-2">
                <span class="fs-4 fw-bold">Voting System</span>
            </a>
            <hr class="d-none d-md-block">

            <?php
            // Nav entries are filtered by session role.
            // Rationale: see README.md "Conversion notes and gotchas".
            $is_admin = $this->session->userdata('role') === 'admin';

            $nav_items = array(
                'home' => array('url' => 'dashboard', 'icon' => 'house', 'label' => 'Home', 'admin' => FALSE),
                'users' => array('url' => 'users', 'icon' => 'people', 'label' => 'View Users', 'admin' => TRUE),
                'elections' => array('url' => 'elections', 'icon' => 'check2-square', 'label' => 'Elections', 'admin' => TRUE),
                'candidates' => array('url' => 'candidates', 'icon' => 'person-badge', 'label' => 'Candidates', 'admin' => FALSE),
                'reports' => array('url' => 'reports', 'icon' => 'bar-chart-line', 'label' => 'Reports', 'admin' => FALSE),
            );
            ?>

            <ul class="nav nav-pills flex-column mb-auto">
                <?php foreach ($nav_items as $key => $item): ?>
                    <?php if ($item['admin'] && ! $is_admin) continue; ?>
                    <li class="nav-item">
                        <a href="<?php echo site_url($item['url']); ?>"
                           class="nav-link<?php echo $active === $key ? ' active' : ''; ?>"
                           <?php echo $active === $key ? 'aria-current="page"' : ''; ?>>
                            <i class="bi bi-<?php echo $item['icon']; ?>"></i>
                            <?php echo $item['label']; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <div class="w-100 d-flex flex-column overflow-auto">

        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom px-3 px-md-4 shadow-sm topbar">
            <div class="container-fluid p-0">

                <button class="navbar-toggler d-md-none border-0 px-1 me-2" type="button"
                        data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu"
                        aria-controls="sidebarMenu" aria-label="Open navigation">
                    <i class="bi bi-list text-dark fs-4"></i>
                </button>

                <span class="navbar-brand mb-0 h1 fw-bold fs-5 fs-md-4 text-truncate"><?php echo html_escape($page_heading ?? $title); ?></span>

                <div class="d-flex align-items-center ms-auto">
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle"
                           id="topbarUserDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle text-muted me-1 me-md-2 fs-5"></i>
                            <strong class="d-none d-sm-inline">
                                <?php
                                // The legacy topbar hard coded the name
                                // "Admin User" regardless of who was signed in.
                                echo html_escape($user_name ?: $this->session->userdata('email'));
                                ?>
                            </strong>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="topbarUserDropdown">
                            <li>
                                <span class="dropdown-item-text small text-muted text-capitalize">
                                    <?php echo html_escape($this->session->userdata('role')); ?>
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
            </div>
        </nav>

        <main class="container-fluid p-3 p-md-4">
