<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AdminLayout extends Component
{
    public string $title;
    public array $breadcrumbs;

    public function __construct(?string $title = null, array $breadcrumbs = [])
    {
        $this->title = $title ?? config('app.name', 'Laravel');
        $this->breadcrumbs = $breadcrumbs;
    }

    public function render()
    {
        // Usa la vista del componente en resources/views/components/admin-layout.blade.php
        return view('components.admin-layout');
    }
}