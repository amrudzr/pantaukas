<?php

/**
 * File: app/views/layout/navbar.php
 * Deskripsi: Navbar umum untuk visitor (belum login), tampil di semua halaman kecuali login dan register.
 */
?>

<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm sticky-top">
    <div class="container">
        <!-- Brand -->
        <a class="navbar-brand fw-bold text-primary" href="/">
            Pantaukas
        </a>

        <!-- Toggle Button Mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
            aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Content -->
        <div class="collapse navbar-collapse" id="mainNavbar">
            <!-- Left Menu -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link<?= $_SERVER['REQUEST_URI'] == '/' ? ' active' : '' ?>" href="/">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?= $_SERVER['REQUEST_URI'] == '/about' ? ' active' : '' ?>" href="/about">Tentang Kami</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?= $_SERVER['REQUEST_URI'] == '/contact' ? ' active' : '' ?>" href="/contact">Kontak</a>
                </li>
            </ul>

            <!-- Right Menu -->
            <div class="d-flex gap-2">
                <a href="/login" class="btn btn-outline-primary">Login</a>
                <a href="/register" class="btn btn-primary">Registrasi</a>
            </div>
        </div>
    </div>
</nav>