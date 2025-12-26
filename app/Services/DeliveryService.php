<?php

namespace App\Services;

use App\DeliveryStatus;
use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Carbon;

class DeliveryService
{
    public function getOrCreateDraft(User $user): Delivery
    {
        return Delivery::firstOrCreate(
            [
                'created_by' => $user->id,
                'status' => DeliveryStatus::Draft,
            ],
            [
                'identifier' => $this->generateIdentifier(),
            ]
        );
    }

    public function generateIdentifier(): string
    {
        $year = Carbon::now()->year;
        $lastDelivery = Delivery::where('identifier', 'like', "DEL-{$year}-%")
            ->orderByRaw('CAST(SUBSTRING(identifier, -4) AS UNSIGNED) DESC')
            ->first();

        if ($lastDelivery && preg_match('/-(\d+)$/', $lastDelivery->identifier, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        } else {
            $nextNumber = 1;
        }

        return sprintf('DEL-%s-%04d', $year, $nextNumber);
    }

    public function submitDelivery(Delivery $delivery): void
    {
        if ($delivery->status !== DeliveryStatus::Draft) {
            throw new \Exception('Only draft deliveries can be submitted.');
        }

        $delivery->update([
            'status' => DeliveryStatus::Submitted,
            'submitted_at' => now(),
        ]);
    }

    public function approveDelivery(Delivery $delivery, User $user): void
    {
        if ($delivery->status !== DeliveryStatus::Submitted) {
            throw new \Exception('Only submitted deliveries can be approved.');
        }

        $delivery->update([
            'status' => DeliveryStatus::Approved,
            'approved_at' => now(),
        ]);
    }

    public function finalizeDelivery(Delivery $delivery, User $user): void
    {
        if ($delivery->status !== DeliveryStatus::Approved) {
            throw new \Exception('Only approved deliveries can be finalized.');
        }

        $delivery->update([
            'status' => DeliveryStatus::Delivered,
            'finalized_at' => now(),
        ]);
    }

    public function deleteDelivery(Delivery $delivery): void
    {
        // Only allow deletion of submitted deliveries
        if ($delivery->status !== DeliveryStatus::Submitted) {
            throw new \Exception('Only submitted deliveries can be deleted.');
        }

        // Delete related items first
        $delivery->items()->delete();
        
        // Delete the delivery
        $delivery->delete();
    }
}

