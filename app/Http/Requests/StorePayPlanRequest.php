<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StorePayPlanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (Auth::check()) {
            return true;
        }
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'dates.*' => 'required|distinct|after:' . date('Y-m-d'),
        ];
    }

    public function messages()
    {
        return [
            'dates.*.required' => __('text.fill_all_dates'),
            'dates.*.distinct' => __('text.distinct_dates'),
            'dates.*.after' => __('text.future_dates'),
        ];
    }
}
