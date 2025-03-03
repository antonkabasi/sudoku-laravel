# Sudoku Web App

## Overview

This repository contains a web-based **Sudoku** application built using Laravel. Originally developed in C# using Windows Forms (in Croatian) for an Object‑Oriented Programming course at the Faculty of Science, University of Split in 2015, this project has been completely re‑implemented as a modern **Laravel** application.

Key new features include:
- **Top‑10 Leaderboard:** Your time is compared against other scores for each difficulty level. If your time qualifies, you'll be prompted to submit your score.
- **CRUD Functionality:** Manage leaderboard entries (create, read, update, and delete) directly from the web interface.
- **Responsive UI:** Built with Laravel Blade, Tailwind CSS, and Bootstrap components for a responsive design.
- **Persistent Database:** Using SQLite for storing leaderboard scores.

## Tech Stack

- **Backend:** Laravel (PHP)
- **Frontend:** Blade Templates, Tailwind CSS, and Bootstrap
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

        composer install
        php artisan key:generate  
        php artisan cache:clear
        php artisan migrate

        php artisan serve


    **Running in Docker**
        TODO



Copyright (c) 2025 Anton Kabaši

This project is licensed under the MIT License. See the LICENSE file for details.