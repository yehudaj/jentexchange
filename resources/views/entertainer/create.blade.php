@extends('layouts.app')

@section('title','Entertainer Signup')

@section('content')
  <div class="card" style="max-width:720px;margin:0 auto;padding:20px;border:1px solid #e5e7eb;border-radius:8px;">
    <h1 style="margin-top:0">Entertainer Signup</h1>
    @if(!empty($oauth_profile))
      <div style="padding:10px;border:1px dashed #d1d5db;border-radius:6px;background:#f8fafc;margin-bottom:12px">
        Signed in with Google as <strong>{{ $oauth_profile['name'] ?? '' }}</strong>
        @if(!empty($oauth_profile['email']))
          (<a href="mailto:{{ $oauth_profile['email'] }}">{{ $oauth_profile['email'] }}</a>)
        @endif
      </div>
    @else
      <p style="color:#6b7280">You can sign up with Google first to prefill this form.</p>
      <p><a href="/oauth/redirect?intent=entertainer">Sign up with Google</a></p>
    @endif

    <form method="POST" action="/entertainer/signup" enctype="multipart/form-data" style="display:grid;grid-template-columns:1fr 1fr;gap:12px;align-items:start">
      @csrf
      <div style="grid-column:1 / span 2">
        <label>Stage Name</label>
        <input name="stage_name" value="{{ old('stage_name', $oauth_profile['name'] ?? '') }}" style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px" />
      </div>

      <div style="grid-column:1 / span 2">
        <label>Bio</label>
        <textarea name="bio" style="width:100%;min-height:120px;padding:8px;border:1px solid #d1d5db;border-radius:6px">{{ old('bio') }}</textarea>
      </div>

      <div>
        <label>Price (USD)</label>
        <input name="price_usd" value="{{ old('price_usd') }}" style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px" />
      </div>

      <div>
        <label>Pricing Notes</label>
        <input name="pricing_notes" value="{{ old('pricing_notes') }}" style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px" />
      </div>

      @php
        $typesList = ['Magician','Mad Science','Novelty Acts','Dancing','Singing / Vocalist','Comedy','Balloon / Face Painter','Puppetry','Kids Shows','Illusions','Other'];
        $selectedTypes = old('types', $oauth_profile['types'] ?? []);
      @endphp
      <div style="grid-column:1 / span 2">
        <label>Type</label>
        <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:6px">
          @foreach($typesList as $t)
            <label style="display:inline-flex;align-items:center;gap:8px;padding:6px 8px;border:1px solid #e5e7eb;border-radius:6px;background:#fff">
              <input type="checkbox" name="types[]" value="{{ $t }}" {{ in_array($t, $selectedTypes) ? 'checked' : '' }} />
              <span>{{ $t }}</span>
            </label>
          @endforeach
          <input id="type-other" name="type_other" placeholder="If Other, describe" value="{{ old('type_other', $oauth_profile['type_other'] ?? '') }}" style="padding:8px;border:1px solid #d1d5db;border-radius:6px;display:none;margin-left:6px" />
        </div>
      </div>

      <div style="grid-column:1 / span 2">
        <label>Audience / Event Types</label>
        <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:6px">
          @php $audiences = ['Boys','Girls','Mixed','Bar Mitzvahs','Bat Mitzvahs','Camps','Professional Events']; $selectedAudiences = old('audiences', []); @endphp
          @foreach($audiences as $t)
            <label style="display:inline-flex;align-items:center;gap:8px;padding:6px 8px;border:1px solid #e5e7eb;border-radius:6px;background:#fff">
              <input type="checkbox" name="audiences[]" value="{{ $t }}" {{ in_array($t, $selectedAudiences) ? 'checked' : '' }} />
              <span>{{ $t }}</span>
            </label>
          @endforeach
        </div>
      </div>

      <div style="grid-column:1 / span 2">
        <label>Areas / Cities Served</label>
        <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:6px">
          @php $cities = ['Brooklyn','Queens','NYC','Long Island','Staten Island','Lakewood NJ','Catskills','Monsey','CT']; @endphp
          @foreach($cities as $c)
            <label style="display:inline-flex;align-items:center;gap:8px;padding:6px 8px;border:1px solid #e5e7eb;border-radius:6px;background:#fff">
              <input type="checkbox" name="cities[]" value="{{ $c }}" {{ in_array($c, old('cities', [])) ? 'checked' : '' }} />
              <span>{{ $c }}</span>
            </label>
          @endforeach
        </div>
      </div>

      <div style="grid-column:1 / span 2">
        <label>Pricing (describe packages / ranges)</label>
        <textarea name="pricing_packages" style="width:100%;min-height:80px;padding:8px;border:1px solid #d1d5db;border-radius:6px">{{ old('pricing_packages', $oauth_profile['pricing'] ?? '') }}</textarea>
      </div>
        @php
          $initialPackages = [];
          if(old('packages_json')) {
            try { $initialPackages = json_decode(old('packages_json'), true) ?: []; } catch(\Exception $e) { $initialPackages = []; }
          } elseif(!empty($oauth_profile['packages'])) {
            $initialPackages = $oauth_profile['packages'];
          }
        @endphp

        <div style="grid-column:1 / span 2">
          <label>Packages</label>
          <div id="packages-list" style="display:flex;flex-direction:column;gap:8px;margin-top:8px"></div>
          <div style="margin-top:8px">
            <button type="button" id="add-package" style="padding:8px 10px;background:#06b6d4;color:#fff;border-radius:6px;border:none">Add Package</button>
          </div>
          <input type="hidden" id="packages_json" name="packages_json" value='@json($initialPackages)' />
        </div>

      <div style="grid-column:1 / span 2">
        <label>Video Links (YouTube/Vimeo) - one per line</label>
        <textarea name="video_links" style="width:100%;min-height:80px;padding:8px;border:1px solid #d1d5db;border-radius:6px">{{ old('video_links') }}</textarea>
      </div>

      <div>
        <label>Profile Picture (upload)</label>
        <input type="file" name="profile_image" accept="image/*" />
      </div>

      <div>
        <label>Background Image (upload)</label>
        <input type="file" name="background_image" accept="image/*" />
      </div>

      <div style="grid-column:1 / span 2;text-align:right">
        <button type="submit" style="padding:10px 16px;background:#0ea5e9;color:#fff;border-radius:6px;border:none">Create Entertainer Profile</button>
      </div>
    </form>
  </div>
@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function(){
    const packagesList = document.getElementById('packages-list');
    const packagesJson = document.getElementById('packages_json');
    const addBtn = document.getElementById('add-package');
    const typeCheckboxes = document.querySelectorAll('input[name="types[]"]');
    const typeOther = document.getElementById('type-other');

    function parseInitial(){
      try { return JSON.parse(packagesJson.value || '[]'); } catch(e) { return []; }
    }

    function render(){
      packagesList.innerHTML = '';
      const pkgs = parseInitial();
      pkgs.forEach((p, idx) => packagesList.appendChild(packageRow(p, idx)));
    }

    function packageRow(pkg, idx){
      const wrapper = document.createElement('div');
      wrapper.style.display = 'grid';
      wrapper.style.gridTemplateColumns = '1fr 120px 60px';
      wrapper.style.gap = '8px';

      const desc = document.createElement('input');
      desc.placeholder = 'Description';
      desc.value = pkg.description || '';
      desc.style.padding = '8px';

      const price = document.createElement('input');
      price.placeholder = 'Price USD';
      price.value = pkg.price || '';
      price.style.padding = '8px';

      const remove = document.createElement('button');
      remove.type = 'button';
      remove.textContent = 'Remove';
      remove.style.padding = '8px';
      remove.style.background = '#ef4444';
      remove.style.color = '#fff';
      remove.style.border = 'none';
      remove.style.borderRadius = '6px';

      remove.addEventListener('click', () => {
        const arr = parseInitial();
        arr.splice(idx,1);
        packagesJson.value = JSON.stringify(arr);
        render();
      });

      [desc, price, remove].forEach(n => wrapper.appendChild(n));

      [desc, price].forEach(el => el.addEventListener('input', () => {
        const arr = parseInitial();
        arr[idx] = { description: desc.value, price: price.value };
        packagesJson.value = JSON.stringify(arr);
      }));

      return wrapper;
    }

    addBtn.addEventListener('click', () => {
      const arr = parseInitial();
      arr.push({ description: '', price: '' });
      packagesJson.value = JSON.stringify(arr);
      render();
    });

    // ensure at least one package
    if(parseInitial().length === 0){
      packagesJson.value = JSON.stringify([{ description: '', price: '' }]);
    }
    render();

    // type 'Other' behavior (checkbox based)
    function toggleOther(){
      if(!typeOther) return;
      const otherChecked = Array.from(typeCheckboxes || []).some(cb => cb.value === 'Other' && cb.checked);
      typeOther.style.display = otherChecked ? 'inline-block' : 'none';
    }
    if(typeCheckboxes && typeCheckboxes.length){
      typeCheckboxes.forEach(cb => cb.addEventListener('change', toggleOther));
      toggleOther();
    }

    // serialize before submit
    const form = packagesList.closest('form');
    if(form) form.addEventListener('submit', () => {
      const inputs = packagesList.querySelectorAll('input');
      const arr = [];
      for(let i=0;i<inputs.length;i+=2){
        const d = inputs[i].value.trim();
        const p = inputs[i+1].value.trim();
        if(d || p) arr.push({ description: d, price: p });
      }
      packagesJson.value = JSON.stringify(arr);
    });
  });
</script>
@endpush

@endsection
