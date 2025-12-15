@extends('layouts.app')

@section('title','Edit Entertainer')

@section('content')
  <div class="card" style="max-width:900px;margin:18px auto;padding:24px;border-radius:12px;background:#ffffff;box-shadow:0 6px 24px rgba(15,23,42,0.06);">
    <h1 style="margin-top:0;margin-bottom:4px;font-size:22px;color:#0f172a">Edit Entertainer Profile</h1>
    <p style="margin-top:0;margin-bottom:18px;color:#475569">Update your public profile, images and service packages. Packages are saved as structured JSON.</p>

    <form method="POST" action="/entertainer/{{ $entertainer->id }}" enctype="multipart/form-data" style="display:grid;grid-template-columns:1fr 1fr;gap:12px;align-items:start">
      @csrf
      @method('PUT')

      <div style="grid-column:1 / span 2">
        <label>Stage Name</label>
        <input name="stage_name" value="{{ old('stage_name', $entertainer->stage_name) }}" style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px" />
      </div>

        <!-- Header: background image and avatar with edit icons -->
        <div style="grid-column:1 / span 2;position:relative;margin-bottom:18px;padding-bottom:48px">
          <div style="height:220px;border-radius:10px;overflow:hidden;position:relative;background:#f3f4f6;">
            @php $bg = $entertainer->background_image_path ?? null; @endphp
            <div style="width:100%;height:100%;background-size:cover;background-position:center;" @if($entertainer->background_image_url) data-bg="{{ $entertainer->background_image_url }}" @endif>
              @if($entertainer->background_image_url)
                  <img src="{{ $entertainer->background_image_url }}" alt="Background" style="width:100%;height:100%;object-fit:cover;display:block" />
              @else
                <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#94a3b8">No background image</div>
              @endif
            </div>
            <button id="edit-bg-btn" type="button" title="Edit background" style="position:absolute;right:12px;top:12px;background:rgba(0,0,0,0.5);border:none;color:#fff;padding:8px;border-radius:8px;cursor:pointer">✎</button>
          </div>

          <div style="position:absolute;left:20px;bottom:0;display:flex;align-items:center;gap:12px">
            <div style="width:96px;height:96px;border-radius:9999px;overflow:hidden;border:4px solid #fff;background:#fff">
              @php $pf = $entertainer->profile_image_path ?? null; @endphp
              @if($entertainer->profile_image_url)
                  <img id="avatar-img" src="{{ $entertainer->profile_image_url }}" alt="Avatar" style="width:100%;height:100%;object-fit:cover;display:block" />
              @else
                <div id="avatar-img" style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#94a3b8">A</div>
              @endif
            </div>
            <button id="edit-avatar-btn" type="button" title="Edit avatar" style="background:rgba(0,0,0,0.5);border:none;color:#fff;padding:6px 10px;border-radius:8px;cursor:pointer">✎ Edit</button>
          </div>
        </div>

      <div style="grid-column:1 / span 2">
        <label>Bio</label>
        <textarea name="bio" style="width:100%;min-height:120px;padding:8px;border:1px solid #d1d5db;border-radius:6px">{{ old('bio', $entertainer->bio) }}</textarea>
      </div>

      <!-- Removed single price fields: packages replace them -->

      @php
        $typesList = ['Magician','Mad Science','Novelty Acts','Dancing','Singing / Vocalist','Comedy','Balloon / Face Painter','Puppetry','Kids Shows','Illusions','Other'];
        $selectedTypes = old('types', $entertainer->types ?? []);
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
          <input id="type-other" name="type_other" placeholder="If Other, describe" value="{{ old('type_other', $entertainer->type_other ?? '') }}" style="padding:8px;border:1px solid #d1d5db;border-radius:6px;display:none;margin-left:6px" />
        </div>
      </div>

      <!-- Cropper Modal -->
      <div id="cropper-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);align-items:center;justify-content:center;z-index:10000">
        <div style="background:#fff;padding:12px;border-radius:8px;max-width:900px;width:90%;max-height:90%;overflow:auto">
          <h3 style="margin-top:0">Crop Image</h3>
          <div style="display:flex;gap:12px;align-items:flex-start">
            <div style="flex:1">
              <img id="crop-source" src="" style="max-width:100%;display:block;" />
            </div>
            <div style="width:260px">
              <div style="margin-bottom:8px">Preview</div>
              <div id="crop-preview" style="width:220px;height:220px;overflow:hidden;border:1px solid #e5e7eb"></div>
              <div style="margin-top:12px;display:flex;gap:8px">
                <button id="crop-upload" type="button" class="btn">Crop & Upload</button>
                <button id="crop-cancel" type="button" class="btn">Cancel</button>
              </div>
            </div>
          </div>
        </div>
      </div>

        
        <div style="grid-column:1 / span 2">
          <label>Audience / Event Types</label>
          <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:6px">
            @php $audiences = ['Boys','Girls','Mixed','Bar Mitzvahs','Bat Mitzvahs','Camps','Professional Events']; $selectedAudiences = old('audiences', $entertainer->audiences ?? []); @endphp
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
          @php $cities = ['Brooklyn','Queens','NYC','Long Island','Staten Island','Lakewood NJ','Catskills','Monsey','CT']; $selectedCities = old('cities', $entertainer->cities ?? []); @endphp
          @foreach($cities as $c)
            <label style="display:inline-flex;align-items:center;gap:8px;padding:6px 8px;border:1px solid #e5e7eb;border-radius:6px;background:#fff">
              <input type="checkbox" name="cities[]" value="{{ $c }}" {{ in_array($c, $selectedCities) ? 'checked' : '' }} />
              <span>{{ $c }}</span>
            </label>
          @endforeach
        </div>
      </div>

      @php
        $initialPackages = [];
        if(old('packages_json')) {
          try { $initialPackages = json_decode(old('packages_json'), true) ?: []; } catch(\Exception $e) { $initialPackages = []; }
        } elseif(!empty($entertainer->pricing_packages)) {
          $initialPackages = $entertainer->pricing_packages;
        }
      @endphp

      <div style="grid-column:1 / span 2">
        <label style="display:block;margin-bottom:8px;font-weight:600;color:#0f172a">Packages</label>
        <div id="packages-list" style="display:flex;flex-direction:column;gap:12px;margin-top:8px"></div>
        <div style="margin-top:10px">
          <button type="button" id="add-package" style="padding:10px 12px;background:#0ea5e9;color:#fff;border-radius:8px;border:none;box-shadow:0 2px 6px rgba(14,165,233,0.18)">Add Package</button>
        </div>
        <input type="hidden" id="packages_json" name="packages_json" value='@json($initialPackages)' />
      </div>

      <div style="grid-column:1 / span 2">
        <label>Video Links (YouTube/Vimeo) - one per line</label>
        <textarea name="video_links" style="width:100%;min-height:80px;padding:8px;border:1px solid #d1d5db;border-radius:6px">{{ old('video_links', isset($entertainer->video_links) ? implode("\n", $entertainer->video_links) : '') }}</textarea>
      </div>
      
        <div>
          <label>Profile Picture (upload)</label>
          @if(!empty($entertainer->profile_image_path))
              <div style="margin-bottom:6px"><img src="{{ Storage::url($entertainer->profile_image_path) }}" alt="Profile" style="max-width:140px;border-radius:6px" /></div>
          @endif
          <input type="file" name="profile_image" accept="image/*" style="display:none" />
        </div>
      
        <div>
          <label>Background Image (upload)</label>
          @if(!empty($entertainer->background_image_path))
              <div style="margin-bottom:6px"><img src="{{ Storage::url($entertainer->background_image_path) }}" alt="Background" style="max-width:240px;border-radius:6px" /></div>
          @endif
          <input type="file" name="background_image" accept="image/*" style="display:none" />
        </div>

      <div style="grid-column:1 / span 2;text-align:right">
        <button type="submit" style="padding:10px 16px;background:#0ea5e9;color:#fff;border-radius:6px;border:none">Update Entertainer Profile</button>
      </div>
    </form>
  </div>
  @push('scripts')
  <script>
    console.log('Entertainer edit: scripts loaded');
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

      // --- Avatar & Background edit helpers ---
      if(form){
        // create hidden file inputs attached to the existing form
        const avatarInput = document.createElement('input');
        avatarInput.type = 'file'; avatarInput.name = 'profile_image'; avatarInput.accept = 'image/*'; avatarInput.style.display = 'none';
        const bgInput = document.createElement('input');
        bgInput.type = 'file'; bgInput.name = 'background_image'; bgInput.accept = 'image/*'; bgInput.style.display = 'none';
        form.appendChild(avatarInput);
        form.appendChild(bgInput);

        const editAvatarBtn = document.getElementById('edit-avatar-btn');
        const editBgBtn = document.getElementById('edit-bg-btn');
        const avatarImg = document.getElementById('avatar-img');

        if(editAvatarBtn) editAvatarBtn.addEventListener('click', () => avatarInput.click());
        if(editBgBtn) editBgBtn.addEventListener('click', () => bgInput.click());

        // AJAX upload helper
        const csrf = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : null;

        async function ajaxUpload(fileInput, fieldName){
          if(!(fileInput.files && fileInput.files[0])) return;
          const fd = new FormData();
          fd.append(fieldName, fileInput.files[0]);
          // add a minimal flag so server knows we're only updating images (optional)
          fd.append('_method','PUT');
          try{
            const res = await fetch(form.action, {
              method: 'POST',
              headers: csrf ? { 'X-CSRF-TOKEN': csrf } : {},
              body: fd,
            });
            if(!res.ok) throw new Error('Upload failed');
            const data = await res.text();
            return data;
          }catch(err){
            console.error('Upload error', err);
            return null;
          }
        }

        avatarInput.addEventListener('change', async () => {
          if(avatarInput.files && avatarInput.files[0]){
            const reader = new FileReader();
            reader.onload = function(e){ if(avatarImg && avatarImg.tagName === 'IMG') avatarImg.src = e.target.result; };
            reader.readAsDataURL(avatarInput.files[0]);
            await ajaxUpload(avatarInput, 'profile_image');
          }
        });

        bgInput.addEventListener('change', async () => {
          if(bgInput.files && bgInput.files[0]){
            const reader = new FileReader();
            reader.onload = function(e){
              const headerImg = document.querySelector('div[data-bg] img');
              if(headerImg) headerImg.src = e.target.result;
            };
            reader.readAsDataURL(bgInput.files[0]);
            await ajaxUpload(bgInput, 'background_image');
          }
        });
        
        // Cropper integration: open modal before upload to allow cropping/resizing
        const cropperModal = document.getElementById('cropper-modal');
        const cropSource = document.getElementById('crop-source');
        const cropPreview = document.getElementById('crop-preview');
        const cropUploadBtn = document.getElementById('crop-upload');
        const cropCancelBtn = document.getElementById('crop-cancel');
        let cropper = null;
        let currentField = null;
        let currentFile = null;

        function openCropperFor(file, field){
          currentField = field;
          currentFile = file;
          const url = URL.createObjectURL(file);
          cropSource.src = url;
          cropperModal.style.display = 'flex';
          // initialize cropper after image loads
            cropSource.onload = () => {
            if(cropper) cropper.destroy();
            const opts = { viewMode: 1, autoCropArea: 1, responsive: true };
            // aspect ratio per field
            if(field === 'profile_image') opts.aspectRatio = 1;
            if(field === 'background_image') opts.aspectRatio = 16/6;
            // use Cropper's preview option
            opts.preview = '#crop-preview';
            console.log('Initializing Cropper', opts);
            cropper = new Cropper(cropSource, opts);
          };
        }

        // wire file inputs to open cropper instead of immediate upload
        avatarInput.addEventListener('change', () => {
          if(avatarInput.files && avatarInput.files[0]){
            openCropperFor(avatarInput.files[0], 'profile_image');
          }
        });
        bgInput.addEventListener('change', () => {
          if(bgInput.files && bgInput.files[0]){
            openCropperFor(bgInput.files[0], 'background_image');
          }
        });

        cropCancelBtn.addEventListener('click', () => {
          if(cropper) { cropper.destroy(); cropper = null; }
          cropperModal.style.display = 'none';
          currentField = null; currentFile = null;
        });

        cropUploadBtn.addEventListener('click', async () => {
          if(!cropper || !currentField) return;
          // target sizes
          const target = currentField === 'profile_image' ? {w:400,h:400} : {w:1600,h:600};
          const canvas = cropper.getCroppedCanvas({ width: target.w, height: target.h, imageSmoothingQuality: 'high' });
          // use pica to resize for better quality
          const p = window.pica ? window.pica() : null;
          let outCanvas = document.createElement('canvas');
          outCanvas.width = target.w; outCanvas.height = target.h;
          if(p){
            await p.resize(canvas, outCanvas);
          } else {
            // fallback
            outCanvas.getContext('2d').drawImage(canvas, 0, 0, target.w, target.h);
          }

          outCanvas.toBlob(async (blob) => {
            if(!blob) return;
            const fd = new FormData();
            fd.append(currentField, blob, 'upload.jpg');
            fd.append('_method','PUT');
            try{
              const res = await fetch(form.action, { method: 'POST', headers: csrf ? {'X-CSRF-TOKEN': csrf} : {}, body: fd });
              if(!res.ok) throw new Error('Upload failed');
              const json = await res.json();
              // update DOM with returned paths
              function normalizeReturnedUrl(val){
                if(!val) return null;
                try{ const u = new URL(val); return val; }catch(e){
                  // not an absolute URL, assume it's a storage-relative path
                  if(val.startsWith('/storage/')) return val;
                  return '/storage/' + val.replace(/^\//, '');
                }
              }

              if(json.profile_image_path && currentField === 'profile_image'){
                const avatarEl = document.getElementById('avatar-img');
                const url = normalizeReturnedUrl(json.profile_image_path);
                if(avatarEl && avatarEl.tagName === 'IMG' && url) avatarEl.src = url;
              }
              if(json.background_image_path && currentField === 'background_image'){
                const headerImg = document.querySelector('div[data-bg] img');
                const url = normalizeReturnedUrl(json.background_image_path);
                if(headerImg && url) headerImg.src = url;
              }
            }catch(e){ console.error(e); }
            // close
            if(cropper){ cropper.destroy(); cropper = null; }
            cropperModal.style.display = 'none';
            currentField = null; currentFile = null;
          }, 'image/jpeg', 0.9);
        });
      }
    });
  </script>
  @endpush

  @endsection
