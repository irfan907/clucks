<?php

namespace App\Livewire\Receivings;

use App\Models\Delivery;
use App\Services\ReceivingService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Create extends Component
{
    public Delivery $delivery;
    public string $receiving_date;
    public ?string $receiving_note = null;
    public array $items = []; // [delivery_item_id => ['received_quantity' => float, 'item_note' => string]]

    public function mount(Delivery $delivery): void
    {
        $this->delivery = $delivery->load('items.receivedItems');
        $this->receiving_date = now()->toDateString();
        
        // Pre-fill with remaining quantities
        $receivingService = new ReceivingService();
        $remainingQuantities = $receivingService->getRemainingQuantities($delivery);
        
        foreach ($remainingQuantities as $item) {
            $deliveryItem = $item['delivery_item'];
            $this->items[$deliveryItem->id] = [
                'received_quantity' => $item['remaining_quantity'] > 0 ? $item['remaining_quantity'] : 0,
                'item_note' => '',
            ];
        }
    }

    protected function rules(): array
    {
        return [
            'receiving_date' => ['required', 'date'],
            'receiving_note' => ['nullable', 'string'],
            'items.*.received_quantity' => ['required', 'numeric', 'min:0'],
            'items.*.item_note' => ['nullable', 'string'],
        ];
    }

    public function save(): void
    {
        $validated = $this->validate();
        
        // Filter out items with zero quantity
        $itemsData = [];
        foreach ($this->items as $deliveryItemId => $itemData) {
            if ($itemData['received_quantity'] > 0) {
                $itemsData[] = [
                    'delivery_item_id' => $deliveryItemId,
                    'received_quantity' => $itemData['received_quantity'],
                    'item_note' => $itemData['item_note'] ?? null,
                ];
            }
        }

        if (empty($itemsData)) {
            $this->addError('items', 'At least one item must have a received quantity greater than 0.');
            return;
        }

        $receivingService = new ReceivingService();
        $receiving = $receivingService->createReceiving(
            $this->delivery,
            [
                'receiving_date' => $validated['receiving_date'],
                'receiving_note' => $validated['receiving_note'],
                'items' => $itemsData,
            ],
            Auth::user()
        );

        $this->redirect(route('receivings.show', $receiving), navigate: true);
    }

    public function render()
    {
        $receivingService = new ReceivingService();
        $remainingQuantities = $receivingService->getRemainingQuantities($this->delivery);

        return view('livewire.receivings.create', [
            'remainingQuantities' => $remainingQuantities,
        ]);
    }
}
