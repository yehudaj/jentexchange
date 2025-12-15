<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function create()
    {
        return view('customer.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:120',
            'state' => 'nullable|string|max:120',
            'postal_code' => 'nullable|string|max:40',
            'country' => 'nullable|string|max:120',
            'customer_type' => 'required|in:personal,business',
            'company_name' => 'nullable|string|max:255',
        ]);

        $data['user_id'] = Auth::id();

        Customer::create($data);
        // Ensure user has customer role
        $user = Auth::user();
        if ($user && ! $user->hasRole(\App\Models\User::ROLE_CUSTOMER)) {
            $user->addRole(\App\Models\User::ROLE_CUSTOMER);
        }
        return redirect('/')->with('status','Customer profile created');
    }

    public function edit(Customer $customer)
    {
        $this->authorize('update', $customer);
        return view('customer.edit', ['customer'=>$customer]);
    }

    public function update(Request $request, Customer $customer)
    {
        $this->authorize('update', $customer);
        $data = $request->validate([
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:120',
            'state' => 'nullable|string|max:120',
            'postal_code' => 'nullable|string|max:40',
            'country' => 'nullable|string|max:120',
            'customer_type' => 'required|in:personal,business',
            'company_name' => 'nullable|string|max:255',
        ]);

        $customer->update($data);
        return redirect('/')->with('status','Customer profile updated');
    }
}
