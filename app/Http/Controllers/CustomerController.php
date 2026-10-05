<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CustomerService;

class CustomerController extends Controller
{
    protected $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function index(Request $request)
    {
        try {
            $customers = $this->customerService->getAllCustomers($request);
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Customers fetched successfully.',
                    'data' => $customers,
                ], 200);
            }
            return view('Customers.index', compact('customers'));
        } catch (\Exception $e) {
            // Handle the exception, log it, or display an error message
            return redirect()->back()->with('error', 'An error occurred while fetching customers.');
        }
    }
}
