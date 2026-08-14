<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreClientRequest;
use App\Http\Requests\Backend\UpdateClientRequest;
use App\Models\ActivityLog;
use App\Models\Client;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ClientController extends Controller
{
    public $user;

    protected ImageUploadService $imageUploadService;

    public function __construct(ImageUploadService $imageUploadService)
    {
        $this->imageUploadService = $imageUploadService;

        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $user = Auth::guard('admin')->user();

        if (is_null($user) || ! $user->can('clients.view')) {
            abort(403, 'Sorry !! You are unauthorized to view any client !');
        }

        $query = Client::ordered();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('client_name', 'like', '%'.$request->q.'%')
                  ->orWhere('company_name', 'like', '%'.$request->q.'%');
            });
        }

        $clients = $query->paginate(15)->withQueryString();

        return view('backend.clients.index', compact('clients'));
    }

    public function create()
    {
        $user = Auth::guard('admin')->user();

        if (is_null($user) || ! $user->can('clients.create')) {
            abort(403, 'Sorry !! You are unauthorized to create any client !');
        }

        return view('backend.clients.create');
    }

    public function store(StoreClientRequest $request)
    {
        $user = Auth::guard('admin')->user();

        if (is_null($user) || ! $user->can('clients.create')) {
            abort(403, 'Sorry !! You are unauthorized to create any client !');
        }

        $data = $request->validated();

        $client = new Client($data);
        $client->status = $request->boolean('status', true);
        $client->sort_order = $data['sort_order'] ?? 0;

        if ($request->hasFile('company_logo')) {
            $client->company_logo = $this->imageUploadService->upload($request->file('company_logo'), 'clients');
        }

        $client->save();

        Cache::forget('clients_count');
        ActivityLog::record('created', 'Clients', $client->id, "Created client \"{$client->client_name}\".");

        session()->flash('success', $client->client_name.' has been created !!');
        return redirect()->route('admin.clients.index');
    }

    public function edit(int $id)
    {
        $user = Auth::guard('admin')->user();

        if (is_null($user) || ! $user->can('clients.edit')) {
            abort(403, 'Sorry !! You are unauthorized to edit any client !');
        }

        $client = Client::findOrFail($id);

        return view('backend.clients.edit', compact('client'));
    }

    public function update(UpdateClientRequest $request, int $id)
    {
        $user = Auth::guard('admin')->user();

        if (is_null($user) || ! $user->can('clients.edit')) {
            abort(403, 'Sorry !! You are unauthorized to edit any client !');
        }

        $client = Client::findOrFail($id);
        $data = $request->validated();

        $client->fill($data);
        $client->status = $request->boolean('status');
        $client->sort_order = $data['sort_order'] ?? $client->sort_order;

        if ($request->hasFile('company_logo')) {
            $client->company_logo = $this->imageUploadService->replace($request->file('company_logo'), 'clients', $client->company_logo);
        }

        $client->save();

        ActivityLog::record('updated', 'Clients', $client->id, "Updated client \"{$client->client_name}\".");

        session()->flash('success', $client->client_name.' has been updated !!');
        return redirect()->route('admin.clients.index');
    }

    public function destroy(int $id)
    {
        $user = Auth::guard('admin')->user();

        if (is_null($user) || ! $user->can('clients.delete')) {
            abort(403, 'Sorry !! You are unauthorized to delete any client !');
        }

        $client = Client::find($id);

        if (! is_null($client)) {
            $this->imageUploadService->delete($client->company_logo);
            $client->delete();

            ActivityLog::record('deleted', 'Clients', $id, "Deleted client \"{$client->client_name}\".");
        }

        session()->flash('success', 'Client has been deleted !!');
        return back();
    }

    /**
     * Bulk delete selected clients (checkbox list on the index table).
     */
    public function bulkDestroy(Request $request)
    {
        $user = Auth::guard('admin')->user();

        if (is_null($user) || ! $user->can('clients.delete')) {
            abort(403);
        }

        $ids = (array) $request->input('ids', []);

        Client::whereIn('id', $ids)->get()->each(function (Client $client) {
            $this->imageUploadService->delete($client->company_logo);
            $client->delete();
        });

        session()->flash('success', count($ids).' client(s) have been deleted !!');
        return back();
    }

    /**
     * Toggle publish / unpublish via AJAX.
     */
    public function toggleStatus(int $id)
    {
        $user = Auth::guard('admin')->user();

        if (is_null($user) || ! $user->can('clients.edit')) {
            abort(403);
        }

        $client = Client::findOrFail($id);
        $client->status = ! $client->status;
        $client->save();

        return response()->json([
            'success' => true,
            'status'  => $client->status,
            'message' => 'Client status updated successfully.',
        ]);
    }
}
