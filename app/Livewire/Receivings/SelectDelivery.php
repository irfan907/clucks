<?php

namespace App\Livewire\Receivings;

use App\DeliveryStatus;
use App\Models\Delivery;
use Livewire\Component;

class SelectDelivery extends Component
{
    public ?string $search = null;

    public function render()
    {
        $query = Delivery::where('status', DeliveryStatus::Approved)
            ->with(['creator', 'items'])
            ->orderBy('created_at', 'desc');

        if ($this->search) {
            $query->where('identifier', 'like', '%' . $this->search . '%');
        }

        $deliveries = $query->get();

        return view('livewire.receivings.select-delivery', [
            'deliveries' => $deliveries,
        ]);
    }
}
