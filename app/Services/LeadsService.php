<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;



class LeadsService
{
    public function getAllLeads($request)
    {
        $leads = Lead::query()->where('status', '!=', 'Won')
            ->with(['assignedUser'])
            ->when($request->search, function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('company', 'like', "%{$search}%");
                });
            })
            ->when($request->status, function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->source, function ($query) use ($request) {
                $query->where('source', $request->source);
            })
            ->when($request->assigned_to, function ($query) use ($request) {
                $query->where('assigned_to', $request->assigned_to);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $users = User::query()
            ->orderBy('name')
            ->get();

        return compact('leads', 'users');
    }

    // public function storeLead($request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|email|unique:leads,email',
    //         'phone' => 'nullable|string|max:20',
    //         'company' => 'nullable|string|max:255',
    //         'status' => 'required|string|in:New,In Progress,Won,Lost',
    //         'source' => 'nullable|string|in:Web,Ads,Referral',
    //         'assigned_to' => 'nullable|exists:users,id',
    //         'follow_up_date' => 'nullable|date',
    //         'notes' => 'nullable|string',
    //     ]);

    //     if ($validator->fails()) {
    //         throw new ValidationException($validator);
    //     }

    //     return Lead::create($validator->validated());
    // }

    public function storeLead($request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:leads,email',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'status' => 'required|string|in:New,In Progress,Won,Lost',
            'source' => 'nullable|string|in:Web,Ads,Referral',
            'assigned_to' => 'nullable|exists:users,id',
            'follow_up_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $data = $validator->validated();

        return DB::transaction(function () use ($data) {

          
            $lead = Lead::create($data);


         
            if ($lead->status === 'Won') {

                $customer = Customer::create([
                    'name' => $lead->name,
                    'email' => $lead->email,
                    'phone' => $lead->phone,
                    'company' => $lead->company,
                ]);


              
                $lead->update([
                    'customer_id' => $customer->id,
                ]);
            }

            return $lead->fresh();
        });
    }

   

    public function updateLead($request, $lead)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',

            'email' => [
                'required',
                'email',
                Rule::unique('leads', 'email')->ignore($lead->id),
            ],

            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'status' => 'required|string|in:New,In Progress,Won,Lost',
            'source' => 'nullable|string|in:Web,Ads,Referral',
            'assigned_to' => 'nullable|exists:users,id',
            'follow_up_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $data = $validator->validated();

        return DB::transaction(function () use ($lead, $data) {

            $oldStatus = $lead->status;
            $newStatus = $data['status'];

         
            if ($newStatus === 'Won') {

                if ($lead->customer_id) {

                    // Customer already exists → update it
                    $customer = Customer::find($lead->customer_id);

                    if ($customer) {
                        $customer->update([
                            'name' => $lead->name,
                            'email' => $lead->email,
                            'phone' => $lead->phone,
                            'company' => $lead->company,
                        ]);
                    }
                } else {

                    // First time becoming Won → create Customer
                    $customer = Customer::create([
                        'name' => $lead->name,
                        'email' => $lead->email,
                        'phone' => $lead->phone,
                        'company' => $lead->company,
                    ]);

                    $lead->update([
                        'customer_id' => $customer->id,
                    ]);
                }
            }

          
            if ($oldStatus === 'Won' && $newStatus !== 'Won') {

                if ($lead->customer_id) {

                    Customer::where('id', $lead->customer_id)->delete();

                    $lead->update([
                        'customer_id' => null,
                    ]);
                }
            }

            return $lead->fresh();
        });
    }
}
