<?php

namespace App\Livewire\Receivings;

use App\Models\Delivery;
use App\Models\Receiving;
use App\Services\ReceivingService;
use Livewire\Component;

class ReceivingsList extends Component
{
    public Delivery $delivery;

    public function mount(Delivery $delivery): void
    {
        $this->delivery = $delivery->load('receivings.receiver', 'receivings.receivedItems.deliveryItem');
    }

    public function delete(Receiving $receiving): void
    {
        try {
            $receivingService = new ReceivingService();
            $receivingService->deleteReceiving($receiving);
            
            // Refresh the delivery to get updated data
            $this->delivery->refresh();
            $this->delivery->load('receivings.receiver', 'receivings.receivedItems.deliveryItem');
            
            session()->flash('message', __('Receiving deleted successfully.'));
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.receivings.receivings-list');
    }
}
