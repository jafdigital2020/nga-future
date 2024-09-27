<?php

namespace App\Http\Controllers\Admin;

use App\Models\Policy;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PolicyController extends Controller
{
    public function policy()
    {
        $policies = Policy::all();

        return view('admin.policy.policy', compact('policies'));
    }

    public function policyStore(Request $request)
    {
        // Validate the input
        $request->validate([
            'policyTitle' => 'required|string|max:255',
            'policyName' => 'required|string|max:255',
            'policyDescription' => 'required|string',
        ]);

        // Create a new Policy instance
        $policy = new Policy();
        
        // Assign the values to the model
        $policy->policyTitle = $request->input('policyTitle');
        $policy->policyName = $request->input('policyName');
        $policy->policyDescription = $request->input('policyDescription');
        $policy->uploaded_by = Auth::user()->id;
        
        // Handle the file upload
        if ($request->hasFile('policyUpload')) {
            $file = $request->file('policyUpload');
            $filePath = $file->store('policies', 'public'); // Store in 'storage/app/public/policies'
            $policy->policyUpload = $filePath;
        }

        // Save the policy to the database
        $policy->save();

        return redirect()->back()->with('success', 'Policy uploaded successfully!');
    }


}
