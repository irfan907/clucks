<?php

namespace App\Livewire\Deliveries;

use App\DeliveryStatus;
use App\Models\Delivery;
use App\Services\DeliveryService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MyDeliveries extends Component
{
    public function render()
    {
        $user = Auth::user();
        $deliveryService = new DeliveryService();

        $draft = Delivery::where('created_by', $user->id)
            ->where('status', DeliveryStatus::Draft)
            ->first();

        $deliveries = Delivery::where('created_by', $user->id)
            ->where('status', '!=', DeliveryStatus::Draft)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.deliveries.my-deliveries', [
            'draft' => $draft,
            'deliveries' => $deliveries,
        ]);
    }

    public function deleteDraft(Delivery $delivery): void
    {
        if ($delivery->status === DeliveryStatus::Draft && $delivery->created_by === Auth::id()) {
            $delivery->delete();
        }
    }
}
