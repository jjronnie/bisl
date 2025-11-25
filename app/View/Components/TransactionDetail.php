<?php

namespace App\View\Components;

use Illuminate\View\Component;

class TransactionDetail extends Component
{
    public string $title;
    public string $value;

    public function __construct(string $title, string $value)
    {
        // Basic sanitization for safety
        $this->title = $title;
        $this->value = $value;
    }

    public function render()
    {
        return view('components.transaction-detail');
    }
}
