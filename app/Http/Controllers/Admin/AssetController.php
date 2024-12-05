<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\User;
use App\Models\Asset;
use App\Models\UserAsset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AssetController extends Controller
{
    public function asset(Request $request)
    {
        // Fetch distinct values for dropdowns
        $statuses = Asset::select('status')->distinct()->pluck('status');
        $models = Asset::select('model')->distinct()->pluck('model');
        $manufacturers = Asset::select('manufacturer')->distinct()->pluck('manufacturer');

        // Build the base query for assets
        $query = Asset::query();

        // Apply filters if they are set in the request
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('model')) {
            $query->where('model', $request->model);
        }

        if ($request->filled('manufacturer')) {
            $query->where('manufacturer', $request->manufacturer);
        }

        // Execute the query to get the filtered results
        $assets = $query->get();

        return view('admin.asset.asset', compact('assets', 'statuses', 'models', 'manufacturers'));
    }


    public function assetStore(Request $request)
    {
        // Validate the input fields
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'purchase_date' => 'nullable|date',
            'model' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'serial_number' => 'required|string|unique:assets,serial_number',
            'condition' => 'required|string|in:New,Good,Damaged,Under Maintenance',
            'status' => 'required|in:0,1,2', // 0 = Available, 1 = Deployed, 2 = Returned
            'value' => 'nullable|numeric',
        ]);

        try {
            // Map status codes to labels if needed
            $statusMapping = [
                '0' => 'Available',
                '1' => 'Deployed',
                '2' => 'Returned',
            ];

            // Create a new asset with validated data
            $asset = Asset::create([
                'name' => $validatedData['name'],
                'purchase_date' => $validatedData['purchase_date'] ?? null,
                'model' => $validatedData['model'] ?? null,
                'manufacturer' => $validatedData['manufacturer'] ?? null,
                'serial_number' => $validatedData['serial_number'],
                'condition' => $validatedData['condition'],
                'status' => $statusMapping[$validatedData['status']],
                'value' => $validatedData['value'] ?? null,
            ]);

            return redirect()->back()->with('success', 'Asset created successfully');

        } catch (\Exception $e) {
            Log::error('Error creating asset: '.$e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while creating the asset. Please try again.');
        }
    }

    public function assetEdit(Request $request, $id)
    {
        // Custom validation messages
        $messages = [
            'name.required' => 'The asset name is required.',
            'purchase_date.date' => 'The purchase date must be a valid date.',
            'serial_number.required' => 'The serial number is required.',
            'serial_number.unique' => 'The serial number must be unique.',
            'condition.required' => 'The condition is required.',
            'status.required' => 'The status is required.',
            'value.numeric' => 'The value must be a valid number.',
        ];

        // Validation rules
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'purchase_date' => 'nullable|date',
            'model' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'serial_number' => 'required|string|unique:assets,serial_number,' . $id,
            'condition' => 'required|string|in:New,Good,Damaged,Under Maintenance',
            'status' => 'required|string|in:Available,Deployed,Returned',
            'value' => 'nullable|numeric|min:0',
        ], $messages);

        // Check if validation fails
        if ($validator->fails()) {
            // Redirect back with error messages
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            // Find the asset by ID
            $asset = Asset::findOrFail($id);

            // Update asset fields with validated data
            $asset->name = $request->input('name');
            $asset->purchase_date = $request->input('purchase_date');
            $asset->model = $request->input('model');
            $asset->manufacturer = $request->input('manufacturer');
            $asset->serial_number = $request->input('serial_number');
            $asset->condition = $request->input('condition');
            $asset->status = $request->input('status');
            $asset->value = $request->input('value');

            // Save the updated asset
            $asset->save();

            return redirect()->back()->with('success', 'Asset updated successfully.');

        } catch (\Exception $e) {
            // Log the error and redirect back with a failure message
            Log::error('Failed to update asset: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update asset: ' . $e->getMessage());
        }
    }

    public function assetDestroy($id)
    {
        $asset = Asset::findOrFail($id);

        $asset->delete();

        return redirect()->back()->with('success', 'Asset deleted sucessfully!');
    }

    // ** USER ASSET ASSIGN ** //

    public function userAsset()
    {
        $assets = Asset::where('status', 'Available')->get();
        // $users = User::all();
        $users = User::where('status', 'active')->get();
        $assignedAssets = UserAsset::with('asset', 'user')->whereNull('return_date')->get();

        $availableAssets = Asset::where('status', 'Available')
            ->select('name')
            ->groupBy('name')
            ->selectRaw('name, COUNT(*) as count')
            ->get();

        $deployedAssets = Asset::where('status', 'Deployed')
            ->select('name')
            ->groupBy('name')
            ->selectRaw('name, COUNT(*) as count')
            ->get();


        return view('admin.asset.userasset', compact('assets', 'users', 'assignedAssets', 'availableAssets', 'deployedAssets'));
    }

    public function assignAsset(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'user_id' => 'required|exists:users,id',
            'assignment_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        try {
            $asset = Asset::findOrFail($request->asset_id);

            // Check if asset is available
            if ($asset->status !== 'Available') {
                return redirect()->back()->with('error', 'Asset is not available for assignment.');
            }

            // Create a new UserAsset record to link the asset to the user
            UserAsset::create([
                'users_id' => $request->user_id,
                'asset_id' => $asset->id,
                'assign_date' => $request->assignment_date,
                'note' => $request->notes,
            ]);

            // Update the asset status to "Deployed"
            $asset->status = 'Deployed';
            $asset->save();

            return redirect()->back()->with('success', 'Asset assigned to user successfully.');

        } catch (\Exception $e) {
            // Log the error with details for troubleshooting
            Log::error('Failed to assign asset', [
                'error_message' => $e->getMessage(),
                'asset_id' => $request->asset_id,
                'user_id' => $request->user_id,
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Failed to assign asset: ' . $e->getMessage());
        }
    }

    public function returnAsset($assetId)
    {
        try {
            // Find the UserAsset record where the asset is currently assigned
            $userAsset = UserAsset::where('asset_id', $assetId)->whereNull('return_date')->firstOrFail();

            // Update the return date in UserAsset to mark it as returned
            $userAsset->return_date = now();
            $userAsset->save();

            // Update the asset's status back to Available
            $asset = $userAsset->asset;
            $asset->status = 'Available';
            $asset->save();

            return redirect()->back()->with('success', 'Asset returned successfully.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to return asset: ' . $e->getMessage());
        }
    }

    public function viewReturnedAssets()
    {
        // Fetch all assets that have been returned (return_date is not null)
        $returnedAssets = UserAsset::with('asset', 'user')
                                    ->whereNotNull('return_date')
                                    ->get();

        return view('admin.asset.returnasset', compact('returnedAssets'));
    }


}
