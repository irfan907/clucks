<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Services\ReceivingService;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;

class DeliveryPdfController extends Controller
{
    public function download(Delivery $delivery)
    {
        $delivery->load(['creator', 'items.product', 'receivings.receivedItems']);
        
        $receivingService = new ReceivingService();
        $remainingQuantities = $receivingService->getRemainingQuantities($delivery);

        // Configure DomPDF options
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);

        // Render the PDF view
        $html = view('pdf.delivery', [
            'delivery' => $delivery,
            'remainingQuantities' => $remainingQuantities,
        ])->render();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'delivery-' . $delivery->identifier . '.pdf';

        return $dompdf->stream($filename);
    }
}

