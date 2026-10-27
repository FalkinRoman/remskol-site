<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->input('name')),
            'phone' => trim((string) $this->input('phone')),
            'comment' => trim((string) $this->input('comment')) ?: null,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'phone' => ['required', 'string', 'max:32', 'regex:/^[\d\s()+\-]{10,}$/'],
            'service' => ['nullable', 'string', 'max:100'],
            'comment' => ['nullable', 'string', 'max:1000'],
            'photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,heic,heif', 'max:10240'],
            'form_id' => ['nullable', 'in:modal'],
            'agree' => ['accepted'],
            'website' => ['prohibited'],
            'ts' => ['nullable', 'integer'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $digits = preg_replace('/\D+/', '', $this->input('phone', ''));
                if (strlen($digits) < 10 || strlen($digits) > 15) {
                    $validator->errors()->add('phone', 'Укажите корректный номер телефона');
                }

                // Боты отправляют форму мгновенно после загрузки страницы.
                $ts = (int) $this->input('ts');
                if ($ts > 0 && (time() - intdiv($ts, 1000)) < 2) {
                    $validator->errors()->add('name', 'Слишком быстро, попробуйте ещё раз');
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Укажите имя',
            'name.min' => 'Имя слишком короткое',
            'phone.required' => 'Укажите телефон',
            'phone.regex' => 'Укажите корректный номер телефона',
            'agree.accepted' => 'Нужно согласие на обработку персональных данных',
            'photo.mimes' => 'Фото должно быть в формате JPG, PNG, WEBP или HEIC',
            'photo.max' => 'Фото не больше 10 МБ',
            'photo.uploaded' => 'Не удалось загрузить фото, попробуйте файл меньше',
            'website.prohibited' => 'Ошибка отправки',
        ];
    }
}
