<?php

namespace App\Http\Controllers\Admin;

use Log;
use Exception;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;

class LoanController extends Controller
{
    public function loan()
    {
        $users = User::where('status', 'active')->get();
        $loans = Loan::with('user')->get();
        return view('admin.loan.loan', compact('users', 'loans'));
    }

    public function loanStore(Request $request)
    {
        // Custom validation messages
        $messages = [
            'users_id.required' => 'The user field is required.',
            'users_id.exists' => 'The selected user is invalid.',
            'loan_name.required' => 'The loan name is required.',
            'amount.required' => 'The loan amount is required.',
            'amount.numeric' => 'The loan amount must be a number.',
            'payable_in_cutoff.required' => 'The payable cutoff is required.',
            'payable_in_cutoff.integer' => 'The payable cutoff must be an integer.',
            'payable_amount_per_cutoff.required' => 'The payable amount per cutoff is required.',
            'payable_amount_per_cutoff.numeric' => 'The payable amount per cutoff must be a number.',
        ];

        // Create a validator instance
        $validator = Validator::make($request->all(), [
            'users_id' => 'required|exists:users,id',
            'loan_name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'payable_in_cutoff' => 'required|integer|min:1',
            'payable_amount_per_cutoff' => 'required|numeric|min:0',
        ], $messages);

        // Check if validation fails
        if ($validator->fails()) {

            $errorMessages = implode('<br>', $validator->errors()->all());

            return redirect()->back()->with('error', $errorMessages);
        }

        try {
            // Create the loan
            $loan = new Loan();

            $loan->users_id = $request->input('users_id');
            $loan->loan_name = $request->input('loan_name');
            $loan->amount = $request->input('amount');
            $loan->payable_in_cutoff = $request->input('payable_in_cutoff');
            $loan->payable_amount_per_cutoff = $request->input('payable_amount_per_cutoff');
            $loan->status = 'Active';
            $loan->save();


            return redirect()->back()->with('success', 'Loan created successfully.');
        } catch (Exception $e) {
            // Log the error for debugging (optional)
            Log::error('Loan creation failed: ' . $e->getMessage());
            // Return with an error message
            return redirect()->back()->with('error', 'Failed to create loan. Please try again.');
        }

    }

    public function loanComplete(Request $request, $id)
    {
        $loan = Loan::findOrFail($id);

        if ($loan->status === 'Completed') {
            return redirect()->back()->with('error', 'It is already completed.');
        }

        $loan->status = 'Completed';
        $loan->save();

        return redirect()->back()->with('success', 'Status changed to completed.');
    }

    public function loanHold(Request $request, $id)
    {
        $loan = Loan::findOrFail($id);

        if ($loan->status === 'Hold') {
            return redirect()->back()->with('error', 'It is already on hold.');
        }

        $loan->status = 'Hold';
        $loan->save();

        return redirect()->back()->with('success', 'Status changed to hold.');
    }

    public function loanActive(Request $request, $id)
    {
        $loan = Loan::findOrFail($id);

        if ($loan->status === 'Active') {
            return redirect()->back()->with('error', 'It is already Active');
        }

        $loan->status = 'Active';
        $loan->save();

        return redirect()->back()->with('success', 'Status changed to Active.');
    }

    public function loanEdit(Request $request, $id)
    {
        // Custom validation messages
        $messages = [
            'loan_namee.required' => 'The loan name is required.',
            'amounte.required' => 'The loan amount is required.',
            'amounte.numeric' => 'The loan amount must be a number.',
            'payable_in_cutoffe.required' => 'The payable cutoff is required.',
            'payable_in_cutoffe.integer' => 'The payable cutoff must be an integer.',
            'payable_amount_per_cutoffe.required' => 'The payable amount per cutoff is required.',
            'payable_amount_per_cutoffe.numeric' => 'The payable amount per cutoff must be a number.',
        ];

        // Create a validator instance
        $validator = Validator::make($request->all(), [
            'loan_namee' => 'required|string|max:255',
            'amounte' => 'required|numeric|min:0',
            'payable_in_cutoffe' => 'required|integer|min:1',
            'payable_amount_per_cutoffe' => 'required|numeric|min:0',
        ], $messages);

        // Check if validation fails
        if ($validator->fails()) {
            // Get all error messages as a single string
            $errorMessages = implode('<br>', $validator->errors()->all());

            return redirect()->back()->with('error', $errorMessages);
        }

        try {
            $loan = Loan::findOrFail($id);

            $loan->loan_name = $request->input('loan_namee');
            $loan->amount = $request->input('amounte');
            $loan->payable_in_cutoff = $request->input('payable_in_cutoffe');
            $loan->payable_amount_per_cutoff = $request->input('payable_amount_per_cutoffe');
            $loan->save();

            return redirect()->back()->with('success', 'Loan updated successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to update loan: ' . $e->getMessage());
        }
    }

    public function loanDestroy($id)
    {
        $loan = Loan::findOrFail($id);

        $loan->delete();

        return redirect()->back()->with('success', 'Loan deleted sucessfully.');
    }
}
