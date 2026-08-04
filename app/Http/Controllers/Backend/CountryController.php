<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Country;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CountryController extends Controller
{
    public $user;

    public function __construct(private ImageUploadService $imageUploadService)
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('countries.view')) {
            abort(403, 'Sorry !! You are unauthorized to view countries !');
        }

        $query = Country::query()->orderBy('sort_order')->orderBy('name');

        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->q.'%')
                  ->orWhere('code', 'like', '%'.$request->q.'%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status === 'active' ? 1 : 0);
        }

        $countries = $query->paginate(20)->withQueryString();

        return view('backend.countries.index', compact('countries'));
    }

    public function create()
    {
        if (is_null($this->user) || ! $this->user->can('countries.create')) {
            abort(403, 'Sorry !! You are unauthorized to create a country !');
        }

        return view('backend.countries.create');
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('countries.create')) {
            abort(403, 'Sorry !! You are unauthorized to create a country !');
        }

        $validated = $this->validateCountry($request);

        if ($request->hasFile('flag')) {
            $validated['flag'] = $this->imageUploadService->upload($request->file('flag'), 'countries');
        }

        $country = Country::create($validated);

        ActivityLog::record('created', 'Country', $country->id, "Created country \"{$country->name}\".");

        session()->flash('success', 'Country has been created successfully !!');
        return redirect()->route('admin.countries.index');
    }

    public function edit(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('countries.edit')) {
            abort(403, 'Sorry !! You are unauthorized to edit this country !');
        }

        $country = Country::findOrFail($id);

        return view('backend.countries.edit', compact('country'));
    }

    public function update(Request $request, int $id)
    {
        if (is_null($this->user) || ! $this->user->can('countries.edit')) {
            abort(403, 'Sorry !! You are unauthorized to edit this country !');
        }

        $country = Country::findOrFail($id);

        $validated = $this->validateCountry($request, $country->id);

        if ($request->hasFile('flag')) {
            // Replace: remove the old file first so storage doesn't accumulate orphans.
            if ($country->flag) {
                Storage::disk('public')->delete($country->flag);
            }
            $validated['flag'] = $this->imageUploadService->upload($request->file('flag'), 'countries');
        } elseif ($request->boolean('remove_flag')) {
            if ($country->flag) {
                Storage::disk('public')->delete($country->flag);
            }
            $validated['flag'] = null;
        } else {
            // No new file and no removal requested — keep the existing flag.
            unset($validated['flag']);
        }

        $country->update($validated);

        ActivityLog::record('updated', 'Country', $country->id, "Updated country \"{$country->name}\".");

        session()->flash('success', 'Country has been updated successfully !!');
        return redirect()->route('admin.countries.index');
    }

    public function destroy(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('countries.delete')) {
            abort(403, 'Sorry !! You are unauthorized to delete this country !');
        }

        $country = Country::find($id);

        if (! is_null($country)) {
            $name = $country->name;

            if ($country->flag) {
                Storage::disk('public')->delete($country->flag);
            }

            $country->delete();

            ActivityLog::record('deleted', 'Country', $id, "Deleted country \"{$name}\".");
        }

        session()->flash('success', 'Country has been deleted successfully !!');
        return back();
    }

    /**
     * Quick toggle for the status switch in the list view.
     */
    public function toggleStatus(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('countries.edit')) {
            abort(403);
        }

        $country = Country::findOrFail($id);
        $country->status = ! $country->status;
        $country->save();

        return response()->json(['success' => true, 'status' => $country->status]);
    }

    private function validateCountry(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name'       => 'required|string|max:100',
            'code'       => ['required', 'string', 'max:5', Rule::unique('countries', 'code')->ignore($ignoreId)],
            'phone_code' => 'nullable|string|max:10',
            'flag'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'status'     => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);
    }
}