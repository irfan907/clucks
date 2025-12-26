<?php

namespace App\Livewire\Deliveries;

use App\DeliveryStatus;
use App\Models\Delivery;
use Livewire\Component;

use App\Services\DeliveryService;
use Illuminate\Support\Facades\Auth;

class AllDeliveries extends Component
{
    public ?string $statusFilter = null;

    public function render()
    {
        $query = Delivery::where('status', '!=', DeliveryStatus::Draft)
            ->with(['creator', 'items'])
            ->orderBy('created_at', 'desc');

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $deliveries = $query->get();

        return view('livewire.deliveries.all-deliveries', [
            'deliveries' => $deliveries,
        ]);
    }

    public function clearFilter(): void
    {
        $this->statusFilter = null;
    }

    public function approve(Delivery $delivery): void
    {
        $deliveryService = new DeliveryService();
        $deliveryService->approveDelivery($delivery, Auth::user());
        session()->flash('message', __('Delivery approved successfully.'));
    }

    public function delete(Delivery $delivery): void
    {
        try {
            $deliveryService = new DeliveryService();
            $deliveryService->deleteDelivery($delivery);
            session()->flash('message', __('Delivery deleted successfully.'));
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }
}
