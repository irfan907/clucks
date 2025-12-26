<?php

namespace App\Services;

use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\Receiving;
use App\Models\ReceivedItem;
use App\Models\User;
use Illuminate\Support\Collection;

class ReceivingService
{
    public function createReceiving(Delivery $delivery, array $data, User $user): Receiving
    {
        if ($delivery->status !== \App\DeliveryStatus::Approved) {
            throw new \Exception('Only approved deliveries can be received.');
        }

        $receiving = Receiving::create([
            'delivery_id' => $delivery->id,
            'received_by' => $user->id,
            'receiving_date' => $data['receiving_date'] ?? now()->toDateString(),
            'receiving_note' => $data['receiving_note'] ?? null,
            'receiving_sequence' => $this->getNextSequence($delivery),
        ]);

        // Create received items
        foreach ($data['items'] as $itemData) {
            ReceivedItem::create([
                'receiving_id' => $receiving->id,
                'delivery_item_id' => $itemData['delivery_item_id'],
                'received_quantity' => $itemData['received_quantity'],
                'item_note' => $itemData['item_note'] ?? null,
                'recorded_date' => $data['receiving_date'] ?? now()->toDateString(),
            ]);
        }

        return $receiving->load('receivedItems');
    }

    public function calculateReceivedQuantities(Delivery $delivery): Collection
    {
        return $delivery->items->map(function (DeliveryItem $item) {
            $totalReceived = $item->receivedItems()->sum('received_quantity');
            return [
                'delivery_item_id' => $item->id,
                'ordered_quantity' => $item->ordered_quantity,
                'total_received' => $totalReceived,
                'remaining' => max(0, $item->ordered_quantity - $totalReceived),
            ];
        });
    }

    public function getRemainingQuantities(Delivery $delivery): Collection
    {
        return $delivery->items->map(function (DeliveryItem $item) {
            $totalReceived = $item->receivedItems()->sum('received_quantity');
            $remaining = max(0, $item->ordered_quantity - $totalReceived);
            return [
                'delivery_item' => $item,
                'ordered_quantity' => $item->ordered_quantity,
                'total_received' => $totalReceived,
                'remaining_quantity' => $remaining,
            ];
        });
    }

    public function getNextSequence(Delivery $delivery): int
    {
        $lastReceiving = $delivery->receivings()->orderBy('receiving_sequence', 'desc')->first();
        return $lastReceiving ? $lastReceiving->receiving_sequence + 1 : 1;
    }

    public function isDeliveryComplete(Delivery $delivery): bool
    {
        foreach ($delivery->items as $item) {
            $totalReceived = $item->receivedItems()->sum('received_quantity');
            if ($totalReceived < $item->ordered_quantity) {
                return false;
            }
        }
        return true;
    }

    public function deleteReceiving(Receiving $receiving): void
    {
        $delivery = $receiving->delivery;
        
        // Delete all received items first (they will be automatically deleted via cascade, but being explicit)
        $receiving->receivedItems()->delete();
        
        // Delete the receiving
        $receiving->delete();
        
        // Recalculate delivery status
        // If delivery was "Delivered" and is no longer complete, change back to "Approved"
        if ($delivery->status === \App\DeliveryStatus::Delivered) {
            if (!$this->isDeliveryComplete($delivery)) {
                $delivery->update([
                    'status' => \App\DeliveryStatus::Approved,
                    'finalized_at' => null,
                ]);
            }
        }
        
        // Note: Stock calculations will automatically update since StockService queries ReceivedItem directly
        // No need to manually recalculate stock
    }
}

