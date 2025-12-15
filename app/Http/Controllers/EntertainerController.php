<?php

namespace App\Http\Controllers;

use App\Models\Entertainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class EntertainerController extends Controller
{
    public function create()
    {
        $oauth = session('oauth_profile', []);
        return view('entertainer.create', ['oauth_profile' => $oauth]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'stage_name' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'price_usd' => 'nullable|numeric',
            'pricing_notes' => 'nullable|string',
            'type' => 'nullable|string',
            'type_other' => 'nullable|string|max:255',
            'types' => 'nullable|array',
            'audiences' => 'nullable|array',
            'cities' => 'nullable|array',
            'pricing_packages' => 'nullable|string',
            'packages_json' => 'nullable|string',
            'video_links' => 'nullable|string',
            'profile_image' => 'nullable|image|max:5120',
            'background_image' => 'nullable|image|max:10240',
        ]);

        $data['user_id'] = Auth::id();
        // normalize fields for storage
        // types: prefer explicit array `types[]` when present, otherwise use single `type` (and optional `type_other`)
        if ($request->has('types')) {
            $data['types'] = $request->input('types', []);
        } else {
            $single = $request->input('type');
            if (!empty($single)) {
                $data['types'] = [$single];
            } elseif ($request->filled('type_other')) {
                $data['types'] = [$request->input('type_other')];
            } else {
                $data['types'] = [];
            }
        }
        $data['cities'] = $request->input('cities', []);
        // audiences (event types) from second checkbox group
        $data['audiences'] = $request->input('audiences', []);
        $data['pricing_packages'] = $request->input('pricing_packages');
        // If packages were submitted as JSON (structured list), decode it and validate small structure
        if ($request->filled('packages_json')) {
            $packagesRaw = $request->input('packages_json');
            $packages = json_decode($packagesRaw, true);
            if (!is_array($packages)) {
                return back()->withErrors(['packages_json' => 'Invalid packages format.'])->withInput();
            }
            $clean = [];
            foreach ($packages as $p) {
                if (!is_array($p)) continue;
                $clean[] = [
                    'price' => isset($p['price']) && $p['price'] !== '' ? floatval($p['price']) : null,
                    'description' => $p['description'] ?? null,
                ];
            }
            $data['pricing_packages'] = $clean;
        }

        // ensure at least one type provided
        $hasTypesArray = !empty($data['types']);
        $hasSingleType = $request->filled('type');
        $hasTypeOther = $request->filled('type_other');
        if (!$hasTypesArray && !$hasSingleType && !$hasTypeOther) {
            return back()->withErrors(['type' => 'Please select at least one type or enter an Other.'])->withInput();
        }
        // video links: store as array of strings
        $videoRaw = $request->input('video_links', '');
        $data['video_links'] = array_values(array_filter(array_map('trim', explode("\n", $videoRaw))));

        // handle file uploads (store on the public disk)
        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('entertainer/profile', 'public');
            $data['profile_image_path'] = $path;
        }
        if ($request->hasFile('background_image')) {
            $path = $request->file('background_image')->store('entertainer/backgrounds', 'public');
            $data['background_image_path'] = $path;
        }

        $entertainer = Entertainer::create($data);
        // Ensure user role is entertainer
        $user = Auth::user();
        if ($user && !$user->isEntertainer()) {
            $user->addRole(\App\Models\User::ROLE_ENTERTAINER);
        }

        // clear oauth_profile from session once used
        session()->forget('oauth_profile');

        return redirect('/')->with('status','Entertainer profile created');
    }

    public function edit(Entertainer $entertainer)
    {
        $this->authorize('update', $entertainer);
        return view('entertainer.edit', ['entertainer'=>$entertainer]);
    }

    public function update(Request $request, Entertainer $entertainer)
    {
        $this->authorize('update', $entertainer);

        \Log::info('Entertainer update called', ['id' => $entertainer->id, 'is_ajax' => $request->ajax(), 'content_length' => $request->server('CONTENT_LENGTH')]);

        $data = $request->validate([
            'stage_name' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'price_usd' => 'nullable|numeric',
            'pricing_notes' => 'nullable|string',
            'types' => 'nullable|array',
            'audiences' => 'nullable|array',
            'cities' => 'nullable|array',
            'pricing_packages' => 'nullable|string',
            'video_links' => 'nullable|string',
            'profile_image' => 'nullable|image|max:5120',
            'background_image' => 'nullable|image|max:10240',
        ]);

        // normalize types similar to store
        if ($request->has('types')) {
            $data['types'] = $request->input('types', []);
        } else {
            $single = $request->input('type');
            if (!empty($single)) {
                $data['types'] = [$single];
            } elseif ($request->filled('type_other')) {
                $data['types'] = [$request->input('type_other')];
            } else {
                $data['types'] = [];
            }
        }

        // handle updated packages JSON with validation
        if ($request->filled('packages_json')) {
            $packagesRaw = $request->input('packages_json');
            $packages = json_decode($packagesRaw, true);
            if (!is_array($packages)) {
                return back()->withErrors(['packages_json' => 'Invalid packages format.'])->withInput();
            }
            $clean = [];
            foreach ($packages as $p) {
                if (!is_array($p)) continue;
                $clean[] = [
                    'price' => isset($p['price']) && $p['price'] !== '' ? floatval($p['price']) : null,
                    'description' => $p['description'] ?? null,
                ];
            }
            $data['pricing_packages'] = $clean;
        }

        // ensure at least one type provided
        $hasTypesArray = !empty($data['types']);
        $hasSingleType = $request->filled('type');
        $hasTypeOther = $request->filled('type_other');
        if (!$hasTypesArray && !$hasSingleType && !$hasTypeOther) {
            return back()->withErrors(['type' => 'Please select at least one type or enter an Other.'])->withInput();
        }

        // ensure audiences set
        $data['audiences'] = $request->input('audiences', $entertainer->audiences ?? []);

        // handle file uploads on update (store on public disk)
        if ($request->hasFile('profile_image')) {
            \Log::info('Profile image upload', ['size' => $request->file('profile_image')->getSize()]);
            $path = $request->file('profile_image')->store('entertainer/profile', 'public');
            $data['profile_image_path'] = $path;
        }
        if ($request->hasFile('background_image')) {
            \Log::info('Background image upload', ['size' => $request->file('background_image')->getSize()]);
            $path = $request->file('background_image')->store('entertainer/backgrounds', 'public');
            $data['background_image_path'] = $path;
        }

        $entertainer->update($data);
        // If AJAX (fetch) request, return JSON with updated image paths
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'profile_image_path' => $entertainer->profile_image_path ? Storage::url($entertainer->profile_image_path) : null,
                'background_image_path' => $entertainer->background_image_path ? Storage::url($entertainer->background_image_path) : null,
                'status' => 'ok'
            ]);
        }

        return redirect('/')->with('status','Entertainer profile updated');
    }
}
