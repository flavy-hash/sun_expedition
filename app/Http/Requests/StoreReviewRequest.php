<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    // Separate bag so review errors don't reopen the inquiry popup on the same page.
    protected $errorBag = 'review';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tour_id' => ['required', 'exists:tours,id'],
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'country' => ['nullable', 'string', 'max:100'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'title' => ['required', 'string', 'max:120'],
            'quote' => ['required', 'string', 'min:20', 'max:3000'],
            'images' => ['nullable', 'array', 'max:4'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'quote.required' => __('Please tell us about your trip.'),
            'quote.min' => __('Your review should be at least 20 characters.'),
            'images.max' => __('You can attach up to 4 photos.'),
            'images.*.image' => __('Each attachment must be a photo.'),
            'images.*.mimes' => __('Photos must be JPG, PNG or WebP.'),
            'images.*.max' => __('Each photo must be 10 MB or smaller.'),
        ];
    }
}
