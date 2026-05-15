<?php

namespace App\Http\Controllers\Api\Site;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ContactController extends Controller
{
    /**
     * Store a new contact message
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'message' => 'required|string|max:5000',
            ]);

            $contact = Contact::create($validated);

            return response()->json([
                'status' => true,
                'data' => ['contact' => $contact],
                'message' => 'تم إرسال رسالتك بنجاح! سنتواصل معك قريباً.'
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'فشل التحقق من البيانات',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Get all contacts (Admin only)
     */
    public function index()
    {
        $contacts = Contact::orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'status' => true,
            'data' => ['contacts' => $contacts],
            'message' => 'Contacts retrieved successfully'
        ]);
    }

    /**
     * Delete a contact message
     */
    public function destroy(Contact $contact)
    {
        $contact->delete();

        return response()->json([
            'status' => true,
            'data' => null,
            'message' => 'Contact deleted successfully'
        ]);
    }
}
