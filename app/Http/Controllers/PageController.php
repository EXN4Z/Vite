<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Inertia\Inertia;

class PageController extends Controller
{
    /**
     * Tampilkan 1 halaman beserta susunan bloknya.
     */
    public function show(Page $page)
    {
        $page->load('blocks');

        return Inertia::render('Pages/Show', [
            'page' => $page,
        ]);
    }
}