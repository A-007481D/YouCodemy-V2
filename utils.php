<?php 

function pd($value) {
    echo '<pre>';
    print_r($value);
    echo '</pre>';
    die();
}

function dd($value) {
    echo '<pre>';
    var_dump($value);
    echo '</pre>';
}


function isInstructor(): bool {
    return isset($_SESSION['user']) && $_SESSION['user']->getRole() === 'instructor';
}

function isStudent(): bool
{
    if (isset($_SESSION['student'])) {
        return true;
    }
    return false;
}

function isAdmin(): bool
{
    return isset($_SESSION['user']) && $_SESSION['user']->getRole() === 'admin';
}


