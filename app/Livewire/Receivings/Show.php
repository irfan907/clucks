<?php

namespace App\Livewire\Receivings;

use App\Models\Receiving;
use App\Services\ReceivingService;
use Livewire\Component;

class Show extends Component
{
    public Receiving $receiving;

    public function mount(Receiving $receiving): void
    {
        $this->receiving = $receiving->load(['delivery', 'receiver', 'receivedItems.deliveryItem']);
    }

    public function delete(): void
    {
        try {
            $receivingService = new ReceivingService();
            $delivery = $this->receiving->delivery;
            $receivingService->deleteReceiving($this->receiving);
            
            session()->flash('message', __('Receiving deleted successfully.'));
            $this->redirect(route('deliveries.show', $delivery), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.receivings.show');
    }
}
