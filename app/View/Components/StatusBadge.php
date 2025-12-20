<?php

namespace App\View\Components;

use Illuminate\View\Component;

class StatusBadge extends Component
{
    public string $status;
    public string $colorClasses;

    public function __construct(string $status)
    {
        $this->status = $status;
        $this->colorClasses = $this->getColorClasses($status);
    }

    private function getColorClasses(string $status): string
    {
        return match ($status) {
            'active' => 'bg-green-100 text-green-700',
            'Silver' => 'bg-gray-100 text-gray-700',
            'Gold' => 'bg-orange-100 text-orange-700',


            'Deposit' => 'bg-green-100 text-green-700',
            'Withdrawal' => 'bg-blue-100 text-blue-700',
            'pending' => 'bg-yellow-100 text-yellow-700',
            'suspended' => 'bg-orange-100 text-orange-700',       
            default => 'bg-gray-100 text-gray-700',
        };
    }

    public function render()
    {
        return view('components.status-badge');
    }
}
