<?php

namespace App\Livewire\Deliveries;

use App\DeliveryStatus;
use App\Models\Delivery;
use App\Services\DeliveryService;
use App\Services\ReceivingService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Show extends Component
{
    public Delivery $delivery;

    public function mount(Delivery $delivery): void
    {
        $this->delivery = $delivery->load(['creator', 'items.product', 'receivings.receivedItems']);
    }

    public function approve(): void
    {
        $receivingService = new ReceivingService();
        $remainingQuantities = $receivingService->getRemainingQuantities($this->delivery);
        if ($remainingQuantities->isEmpty()) {
            $this->addError('remainingQuantities', __('No products to approve.'));
            return;
        }
        
        $deliveryService = new DeliveryService();
        $deliveryService->approveDelivery($this->delivery, Auth::user());
        $this->delivery->refresh();
    }

    public function finalize(): void
    {
        $deliveryService = new DeliveryService();
        $deliveryService->finalizeDelivery($this->delivery, Auth::user());
        $this->delivery->refresh();
    }

    public function render()
    {
        $receivingService = new ReceivingService();
        $remainingQuantities = $receivingService->getRemainingQuantities($this->delivery);

        return view('livewire.deliveries.show', [
            'remainingQuantities' => $remainingQuantities,
        ]);
    }
}
