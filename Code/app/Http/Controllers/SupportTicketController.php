<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\SupportTicket;
use App\Models\User;
use App\Notifications\MessageNotification;
use App\Notifications\SupportTicketCreated;
use App\Notifications\TicketCloseNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportTicketController extends Controller
{
    public function store(Request $request)
    {
        try {
            $ticket = new SupportTicket();
            $ticket->customer_name = $request->input('customer_name');
            $ticket->problem_title = $request->input('problem_title');
            $ticket->problem_description = $request->input('problem_description');
            $ticket->email = $request->input('email');
            $ticket->phone_number = $request->input('phone_number');
            $ticket->reference_number = uniqid();
            $ticket->save();

            // Send email notification
            $ticket->notify(new SupportTicketCreated($ticket));

            return redirect()->route('ticket')->with('success', 'Support ticket created successfully. Your reference number is: ' . $ticket->reference_number);
        } catch (\Exception $e) {
            return redirect()->route('ticket')->with('error', 'An error occurred while creating the support ticket. Please try again later.');
        }
    }

    public function get_tickets()
    {
        try {
            $users_id = Auth::user()->id;
            $user = User::find($users_id);

            if ($user->type === 'admin') {
                // User is an admin, show all records
                $tickets = SupportTicket::get();
            } else {
                // User is not an admin, filter by user's email
                $tickets = SupportTicket::get()
                    ->where('email', $user->email);
            }

            return datatables()::of($tickets)
                ->editColumn('created_at', function ($ticket) {
                    return Carbon::parse($ticket->created_at)->format('Y-m-d H:i:s');
                })
                ->make(true);
        } catch (\Exception $e) {
            // Handle the exception
            return response()->json(['error' => 'An error occurred while fetching ticket data.'], 500);
        }
    }

    public function send_message(Request $request)
    {
        $users_id = Auth::user()->id;
        $ticketId = $request->input('ticket_id');
        $ticket = SupportTicket::find($ticketId);


        if (!$ticket) {
            return redirect()->route('home')->with('error', 'Ticket not found.');
        }


        $message = new Message();
        $message->support_ticket_id = $request->input('ticket_id');
        $message->message = $request->input('message');
        $message->user_id =  $users_id;
        $message->save();

        $message->reference_number = $request->input('reference_number');
        $message->email = $request->input('uemail');

        // Send email notification
        $message->notify(new MessageNotification($message));



        return redirect()->route('home', ['id' => $ticketId])
            ->with('success', 'Message sent successfully.');
    }

    public function get_messages(Request $request)
    {
        try {
            $users_id = Auth::user()->id;
            $ticketId = $request->input('ticket_id');
        
            $messages = Message::where('support_ticket_id', $ticketId)
                ->join('users', 'users.id', '=', 'messages.user_id')
                ->select('messages.*', 'users.name as user_name', 'users.id as user_id')
                ->get();
        
            foreach ($messages as $message) {
                $message->is_user = ($message->user_id == $users_id) ? 1 : 0;
            }
        
            return response()->json( $messages);
        } catch (\Exception $e) {
            // Handle the exception 
            return response()->json(['error' => 'An error occurred while fetching messages.'], 500);
        }
        
        
    }

    public function close($id)
    {
        try {
            $ticket = SupportTicket::findOrFail($id);
            $ticket->status = '1';
            $ticket->save();

            $ticket->notify(new TicketCloseNotification($ticket));
            return redirect()->back()->with('success', 'Ticket has been closed successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while closing the ticket.');
        }
    }
}
