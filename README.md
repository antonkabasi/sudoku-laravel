# Sudoku Web App

## Overview

This repository contains a web-based **Sudoku** application built using Laravel. Originally developed in C# using Windows Forms (in Croatian) for an Object‑Oriented Programming course at the Faculty of Science, University of Split in 2015, this project has been completely re‑implemented as a modern **Laravel** application.

Key new features include:
- **Leaderboard:** Your time is compared against other scores for each difficulty level.
- **CRUD Functionality:** Manage leaderboard entries (create, read, update, and delete) directly from the web interface.
- **Responsive UI:** Built with Laravel Blade, Tailwind CSS, and Bootstrap components for a responsive design.
- **Persistent Database:** Using SQLite for storing leaderboard scores.

## Tech Stack

- **Backend:** Laravel (PHP)
- **Frontend:** Blade, Tailwind CSS, and Bootstrap
- **Database:** SQLite 
- **Containerization:** Docker

## Might Do
- **User Authentication**

## Installation & Running

    1. **Installing locally on Linux, see the docs for other OS: https://laravel.com/docs/12.x/installation**
        
        1. **Clone the repository:**

        git clone https://github.com/antonkabasi/sudoku-laravel.git
        cd sudoku-laravel
        
        2. **Install PHP and the Laravel installer:**
        
        /bin/bash -c "$(curl -fsSL https://php.new/install/linux/8.4)"
        composer global require laravel/installer

        3. **Install Dependencies and key:**

        npm install && npm run build

        composer install
        cp .env.example .env
        php artisan key:generate  

        4. **Generate database (SQLITE):** Choose yes to generate SQLite database

        php artisan migrate

        3. **Run the app:**

        php artisan serve

        Open application in browser at http://127.0.0.1:8000

    **Running in Docker**
        TODO



Copyright (c) 2025 Anton Kabaši

This project is licensed under the MIT License. See the LICENSE file for details.