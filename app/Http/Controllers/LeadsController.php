<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LeadsService;
use Illuminate\Validation\ValidationException;
use App\Models\Lead;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;



class LeadsController extends Controller
{
    protected $leadsService;
    public function __construct(LeadsService $leadsService)
    {
        $this->leadsService = $leadsService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {

            $data = $this->leadsService->getAllLeads($request);

            return view('Leads.index', $data);
        } catch (\Exception $e) {

            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $storedLead = $this->leadsService->storeLead($request);
            return response()->json([
                'message' => 'Lead created successfully',
                'lead' => $storedLead,
                'redirect' => route('leads.index'),
            ], 201);
        } catch (ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lead $lead)
    {
        try {

            $updatedLead = $this->leadsService->updateLead(
                $request,
                $lead
            );

            return response()->json([
                'message' => 'Lead updated successfully',
                'lead' => $updatedLead,
                'redirect' => route('leads.index'),
            ], 200);
        } catch (ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {

            \Log::error('Lead update failed', [
                'lead_id' => $lead->id,
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again.',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $lead = Lead::findOrFail($id);

            $lead->delete();

            return redirect()
                ->route('leads.index')
                ->with('success', 'Lead deleted successfully.');
        } catch (ModelNotFoundException $e) {

            return redirect()
                ->route('leads.index')
                ->with('error', 'Lead not found.');
        } catch (QueryException $e) {

            Log::error('Lead deletion failed', [
                'lead_id' => $id,
                'exception' => $e,
            ]);

            return redirect()
                ->route('leads.index')
                ->with('error', 'Failed to delete lead.');
        } catch (\Exception $e) {

            Log::error('Unexpected lead deletion error', [
                'lead_id' => $id,
                'exception' => $e,
            ]);

            return redirect()
                ->route('leads.index')
                ->with('error', 'Something went wrong. Please try again.');
        }
    }
}
