<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TicketController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum'); // Ensure Sanctum middleware is applied
    }

    public function index()
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: No authenticated user found.',
                ], 401);
            }

            $tickets = Ticket::where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return response()->json([
                'success' => true,
                'data' => $tickets->items(),
                'pagination' => [
                    'total' => $tickets->total(),
                    'per_page' => $tickets->perPage(),
                    'current_page' => $tickets->currentPage(),
                    'last_page' => $tickets->lastPage(),
                    'from' => $tickets->firstItem(),
                    'to' => $tickets->lastItem(),
                    'next_page_url' => $tickets->nextPageUrl(),
                    'prev_page_url' => $tickets->previousPageUrl(),
                ],
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching tickets', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching tickets.',
            ], 500);
        }
    }
    // Store a new ticket
    public function store(Request $request)
    {
        // Log authentication details for debugging
        Log::info('Authentication Check', [
            'user_id' => Auth::id(),
            'is_authenticated' => Auth::check(),
            'user' => Auth::user() ? Auth::user()->toArray() : null,
        ]);

        // Validate request data
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // Check if user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: No authenticated user found.',
            ], 401);
        }

        // Create the ticket
        $ticket = Ticket::create([
            'user_id' => Auth::id(),
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        return response()->json([
            'success' => true,
            'data' => $ticket,
            'message' => 'Ticket created successfully.',
        ], 201);
    }

    public function show($id)
    {
        try {
            // Log for debugging
            Log::info('Fetching ticket', [
                'ticket_id' => $id,
                'user_id' => Auth::id(),
                'is_authenticated' => Auth::check(),
            ]);

            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: No authenticated user found.',
                ], 401);
            }

            $ticket = Ticket::where('user_id', Auth::id())
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $ticket,
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found or you do not have permission to view it.',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error fetching ticket', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching the ticket.',
            ], 500);
        }
    }

}
