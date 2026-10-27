<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PageController extends Controller
{
    public function service(string $slug): View
    {
        $service = config("catalog.services.$slug");

        abort_unless($service, 404);

        return view('pages.service', ['slug' => $slug, 's' => $service]);
    }

    public function works(): View
    {
        return view('pages.works');
    }

    public function prices(): View
    {
        return view('pages.prices');
    }

    public function contacts(): View
    {
        return view('pages.contacts');
    }
}
