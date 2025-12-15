@extends('layouts.app')

@section('title','Edit Customer')

@section('content')
  <div class="card">
    <h1>Edit Customer</h1>
    <form method="POST" action="/customer/{{ $customer->id }}">
      @csrf
      @method('PUT')
      <div>
        <label>Address Line 1</label>
        <input name="address_line1" value="{{ old('address_line1', $customer->address_line1) }}" />
      </div>
      <div>
        <label>Address Line 2</label>
        <input name="address_line2" value="{{ old('address_line2', $customer->address_line2) }}" />
      </div>
      <div>
        <label>City</label>
        <input name="city" value="{{ old('city', $customer->city) }}" />
      </div>
      <div>
        <label>State</label>
        <input name="state" value="{{ old('state', $customer->state) }}" />
      </div>
      <div>
        <label>Postal Code</label>
        <input name="postal_code" value="{{ old('postal_code', $customer->postal_code) }}" />
      </div>
      <div>
        <label>Country</label>
        <input name="country" value="{{ old('country', $customer->country) }}" />
      </div>
      <div>
        <label>Customer Type</label>
        <select name="customer_type">
          <option value="personal" {{ $customer->customer_type=='personal' ? 'selected' : '' }}>Personal</option>
          <option value="business" {{ $customer->customer_type=='business' ? 'selected' : '' }}>Business</option>
        </select>
      </div>
      <div>
        <label>Company Name (if business)</label>
        <input name="company_name" value="{{ old('company_name', $customer->company_name) }}" />
      </div>
      <button type="submit">Update Customer Profile</button>
    </form>
  </div>
@endsection
