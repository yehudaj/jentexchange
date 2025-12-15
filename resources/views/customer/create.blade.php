@extends('layouts.app')

@section('title','Customer Signup')

@section('content')
  <div class="card">
    <h1>Customer Signup</h1>
    <form method="POST" action="/customer/signup">
      @csrf
      <div>
        <label>Address Line 1</label>
        <input name="address_line1" value="{{ old('address_line1') }}" />
      </div>
      <div>
        <label>Address Line 2</label>
        <input name="address_line2" value="{{ old('address_line2') }}" />
      </div>
      <div>
        <label>City</label>
        <input name="city" value="{{ old('city') }}" />
      </div>
      <div>
        <label>State</label>
        <input name="state" value="{{ old('state') }}" />
      </div>
      <div>
        <label>Postal Code</label>
        <input name="postal_code" value="{{ old('postal_code') }}" />
      </div>
      <div>
        <label>Country</label>
        <input name="country" value="{{ old('country') }}" />
      </div>
      <div>
        <label>Customer Type</label>
        <select name="customer_type">
          <option value="personal">Personal</option>
          <option value="business">Business</option>
        </select>
      </div>
      <div>
        <label>Company Name (if business)</label>
        <input name="company_name" value="{{ old('company_name') }}" />
      </div>
      <button type="submit">Create Customer Profile</button>
    </form>
  </div>
@endsection
