<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // We need the member to find the account
            'member_id' => [
                'required',
                'numeric',
                Rule::exists('members', 'id')->whereNull('deleted_at')
            ],
            
            'transaction_type' => [
                'required',
                Rule::in(['deposit', 'withdrawal', 'loan_disbursement', 'loan_repayment', 'fee', 'other'])
            ],
            
            'amount' => ['required', 'numeric', 'min:0.01', 'max:1000000000'], 
            'method' => ['nullable', 'string', 'max:50'],
            'description' => ['required', 'string', 'max:255'],
            'remarks' => ['nullable', 'string', 'max:1000'],
            'transaction_date' => ['required', 'date'], 

            // Handle loan-related transactions
            'loan_id' => [
                'nullable',
                Rule::requiredIf(in_array($this->transaction_type, ['loan_disbursement', 'loan_repayment'])),
                'exists:loans,id'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'loan_id.requiredIf' => 'A Loan ID is required for loan disbursements and repayments.',
            'member_id.exists' => 'The selected member is invalid or has been terminated.',
        ];
    }
}
