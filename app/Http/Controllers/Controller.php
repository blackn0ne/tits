<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller
{
    use AuthorizesRequests;

    protected function adminPerPage(): int
    {
        return 15;
    }

    protected function siteProjectsPerPage(): int
    {
        return 10;
    }

    protected function siteBlogPerPage(): int
    {
        return 12;
    }
}
