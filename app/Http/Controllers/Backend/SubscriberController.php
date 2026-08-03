<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriberController extends Controller
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
        if (is_null($this->user) || ! $this->user->can('subscribers.view')) {
            abort(403, 'Sorry !! You are unauthorized to view any subscriber !');
        }

        $query = NewsletterSubscriber::latest('subscribed_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('email', 'like', '%'.$request->q.'%')
                  ->orWhere('name', 'like', '%'.$request->q.'%');
            });
        }

        $subscribers = $query->paginate(20)->withQueryString();

        return view('backend.subscribers.index', compact('subscribers'));
    }

    public function toggleStatus(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('subscribers.edit')) {
            abort(403);
        }

        $subscriber = NewsletterSubscriber::findOrFail($id);
        $subscriber->status = ! $subscriber->status;
        $subscriber->save();

        return response()->json(['success' => true, 'status' => $subscriber->status]);
    }

    public function destroy(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('subscribers.delete')) {
            abort(403, 'Sorry !! You are unauthorized to delete this subscriber !');
        }

        $subscriber = NewsletterSubscriber::find($id);

        if (! is_null($subscriber)) {
            $subscriber->delete();
            ActivityLog::record('deleted', 'Newsletter Subscribers', $id, "Removed subscriber \"{$subscriber->email}\".");
        }

        session()->flash('success', 'Subscriber has been removed !!');
        return back();
    }

    /**
     * Stream all subscribers as a CSV download (spec §16).
     */
    public function exportCsv()
    {
        if (is_null($this->user) || ! $this->user->can('subscribers.view')) {
            abort(403);
        }

        $filename = 'newsletter-subscribers-'.now()->format('Y-m-d').'.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Email', 'Name', 'Status', 'Subscribed At']);

            NewsletterSubscriber::orderBy('subscribed_at')->chunk(200, function ($chunk) use ($handle) {
                foreach ($chunk as $subscriber) {
                    fputcsv($handle, [
                        $subscriber->email,
                        $subscriber->name,
                        $subscriber->status ? 'Active' : 'Unsubscribed',
                        $subscriber->subscribed_at?->format('Y-m-d H:i'),
                    ]);
                }
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
