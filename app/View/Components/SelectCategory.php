<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SelectCategory extends Component
{
    public $selected;

    public function __construct($selected = null)
    {
        $this->selected = $selected;
    }

    public function render()
    {
        return view('components.select-category', [
            'categories' => config('helpdesk.categories', []),
        ]);
    }
}
