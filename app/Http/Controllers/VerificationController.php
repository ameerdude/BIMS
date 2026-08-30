<?php

namespace App\Http\Controllers;

use App\Models\DocumentIssued;
use App\Models\BarangayId;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function __invoke(string $token)
    {
        // Check if it's a document verification
        $document = DocumentIssued::where('qr_token', $token)->first();

        if ($document) {
            return view('verify', [
                'type' => 'document',
                'document' => $document,
            ]);
        }

        // Check if it's a barangay ID verification
        $barangayId = BarangayId::where('qr_token', $token)->first();

        if ($barangayId) {
            return view('verify', [
                'type' => 'id',
                'barangayId' => $barangayId,
            ]);
        }

        abort(404, 'Verification record not found.');
    }
}
