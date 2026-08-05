<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Admin;
use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportTicketController extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('support-tickets.view')) {
            abort(403, 'Sorry !! You are unauthorized to view support tickets !');
        }

        $query = SupportTicket::with(['requester', 'assignee'])->latest();

        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('subject', 'like', '%'.$request->q.'%')
                  ->orWhere('ticket_number', 'like', '%'.$request->q.'%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->boolean('mine')) {
            $query->where('assigned_to', $this->user->id);
        }

        $tickets = $query->paginate(20)->withQueryString();

        return view('backend.support-tickets.index', compact('tickets'));
    }

    public function create()
    {
        if (is_null($this->user) || ! $this->user->can('support-tickets.create')) {
            abort(403, 'Sorry !! You are unauthorized to create a support ticket !');
        }

        $admins = Admin::orderBy('first_name')->get();

        return view('backend.support-tickets.create', compact('admins'));
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('support-tickets.create')) {
            abort(403, 'Sorry !! You are unauthorized to create a support ticket !');
        }

        $validated = $request->validate([
            'subject'             => 'required|string|max:200',
            'description'         => 'required|string',
            'priority'            => 'required|in:low,medium,high,urgent',
            'category'            => 'nullable|string|max:100',
            'requester_type'      => 'required|in:internal,external',
            'requester_admin_id'  => 'required_if:requester_type,internal|nullable|exists:admins,id',
            'requester_name'      => 'required_if:requester_type,external|nullable|string|max:150',
            'requester_email'     => 'required_if:requester_type,external|nullable|email|max:150',
            'assigned_to'         => 'nullable|exists:admins,id',
        ]);

        $ticket = SupportTicket::create([
            'subject'            => $validated['subject'],
            'description'        => $validated['description'],
            'priority'           => $validated['priority'],
            'category'           => $validated['category'] ?? null,
            'status'             => 'open',
            'requester_admin_id' => $validated['requester_type'] === 'internal' ? $validated['requester_admin_id'] : null,
            'requester_name'     => $validated['requester_type'] === 'external' ? $validated['requester_name'] : null,
            'requester_email'    => $validated['requester_type'] === 'external' ? $validated['requester_email'] : null,
            'assigned_to'        => $validated['assigned_to'] ?? null,
            'created_by'         => $this->user->id,
        ]);

        ActivityLog::record('created', 'SupportTicket', $ticket->id, "Created support ticket \"{$ticket->ticket_number}\".");

        session()->flash('success', 'Support ticket has been created successfully !!');
        return redirect()->route('admin.support-tickets.show', $ticket->id);
    }

    public function show(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('support-tickets.view')) {
            abort(403, 'Sorry !! You are unauthorized to view this support ticket !');
        }

        $ticket = SupportTicket::with(['requester', 'assignee', 'creator', 'replies.admin'])->findOrFail($id);
        $admins = Admin::orderBy('first_name')->get();

        return view('backend.support-tickets.show', compact('ticket', 'admins'));
    }

    public function reply(Request $request, int $id)
    {
        if (is_null($this->user) || ! $this->user->can('support-tickets.view')) {
            abort(403);
        }

        $ticket = SupportTicket::findOrFail($id);

        $validated = $request->validate([
            'message'          => 'required|string',
            'is_internal_note' => 'nullable|boolean',
            'attachment'       => 'nullable|file|max:10240',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('ticket-attachments', 'public');
        }

        SupportTicketReply::create([
            'ticket_id'        => $ticket->id,
            'admin_id'         => $this->user->id,
            'is_internal_note' => $request->boolean('is_internal_note'),
            'message'          => $validated['message'],
            'attachment'       => $attachmentPath,
        ]);

        if ($ticket->status === 'open') {
            $ticket->update(['status' => 'in_progress']);
        }

        ActivityLog::record('replied', 'SupportTicket', $ticket->id, "Replied to support ticket \"{$ticket->ticket_number}\".");

        session()->flash('success', 'Reply has been added !!');
        return back();
    }

    public function updateStatus(Request $request, int $id)
    {
        if (is_null($this->user) || ! $this->user->can('support-tickets.edit')) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);

        $ticket = SupportTicket::findOrFail($id);
        $ticket->status = $validated['status'];
        $ticket->resolved_at = in_array($validated['status'], ['resolved', 'closed']) ? now() : null;
        $ticket->save();

        ActivityLog::record('updated', 'SupportTicket', $ticket->id, "Marked ticket \"{$ticket->ticket_number}\" as {$validated['status']}.");

        session()->flash('success', 'Ticket status updated !!');
        return back();
    }

    public function assign(Request $request, int $id)
    {
        if (is_null($this->user) || ! $this->user->can('support-tickets.edit')) {
            abort(403);
        }

        $validated = $request->validate([
            'assigned_to' => 'nullable|exists:admins,id',
        ]);

        $ticket = SupportTicket::findOrFail($id);
        $ticket->update(['assigned_to' => $validated['assigned_to']]);

        ActivityLog::record('updated', 'SupportTicket', $ticket->id, "Reassigned ticket \"{$ticket->ticket_number}\".");

        session()->flash('success', 'Ticket assignment updated !!');
        return back();
    }

    public function destroy(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('support-tickets.delete')) {
            abort(403, 'Sorry !! You are unauthorized to delete this support ticket !');
        }

        $ticket = SupportTicket::find($id);

        if (! is_null($ticket)) {
            $number = $ticket->ticket_number;
            $ticket->delete();

            ActivityLog::record('deleted', 'SupportTicket', $id, "Deleted support ticket \"{$number}\".");
        }

        session()->flash('success', 'Support ticket has been deleted successfully !!');
        return redirect()->route('admin.support-tickets.index');
    }
}
