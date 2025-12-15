@extends('layouts.app')

@section('title','Admin — Users')

@section('content')
  <div class="card">
    <h1>Users</h1>
    @if(session('status'))
      <div style="padding:8px;background:#e6ffed;border:1px solid #c6f6d5;margin-bottom:12px">{{ session('status') }}</div>
    @endif
    @if(session('error'))
      <div style="padding:8px;background:#fff5f5;border:1px solid #fed7d7;margin-bottom:12px">{{ session('error') }}</div>
    @endif

    <table style="width:100%;border-collapse:collapse">
      <thead>
        <tr style="text-align:left;border-bottom:1px solid #e5e7eb">
          <th>ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Role</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($users as $u)
          <tr style="border-bottom:1px solid #f3f4f6">
            <td>{{ $u->id }}</td>
            <td>{{ $u->name }}</td>
            <td>{{ $u->email }}</td>
            <td>
              @php
                $displayRoles = '—';
                if (method_exists($u, 'roles')) {
                    // relation may be a Collection or array depending on hydration
                    $r = $u->roles;
                    if (is_array($r)) {
                        $displayRoles = count($r) ? implode(', ', array_map(function($it){ return is_array($it) && isset($it['name']) ? $it['name'] : (is_object($it) && isset($it->name) ? $it->name : (string)$it); }, $r)) : '—';
                    } elseif ($r instanceof \Illuminate\Support\Collection) {
                        $displayRoles = $r->pluck('name')->join(', ') ?: '—';
                    } else {
                        // fallback
                        $displayRoles = is_string($u->roles) ? $u->roles : ($u->role ?? '—');
                    }
                } else {
                    $displayRoles = is_array($u->roles) ? implode(', ', $u->roles) : ($u->roles ?? ($u->role ?? '—'));
                }
              @endphp
              {{ $displayRoles }}
            </td>
            <td>
              @if(auth()->id() !== $u->id)
                <form method="POST" action="{{ route('admin.users.delete', ['user' => $u->id]) }}" style="display:inline">
                  @csrf
                  @method('DELETE')
                  <button type="submit" style="background:#f87171;color:#fff;padding:6px 10px;border-radius:6px;border:none">Delete</button>
                </form>
              @else
                —
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
@endsection
