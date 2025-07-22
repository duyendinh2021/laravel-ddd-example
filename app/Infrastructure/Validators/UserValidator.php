<?php

namespace App\Infrastructure\Validators;

use Prettus\Validator\LaravelValidator;
use Prettus\Validator\Contracts\ValidatorInterface;

class UserValidator extends LaravelValidator
{
    /**
     * Validation rules
     */
    protected array $rules = [
        ValidatorInterface::RULE_CREATE => [
            'email' => 'required|email|unique:users,email',
            'password_hash' => 'required|string',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'phone' => 'nullable|string|regex:/^[0-9]{10,11}$/',
            'role' => 'required|in:admin,user,guest',
            'status' => 'required|in:active,inactive,pending',
        ],
        
        ValidatorInterface::RULE_UPDATE => [
            'email' => 'email|unique:users,email,{id}',
            'first_name' => 'string|max:50', 
            'last_name' => 'string|max:50',
            'phone' => 'nullable|string|regex:/^[0-9]{10,11}$/',
            'role' => 'in:admin,user,guest',
            'status' => 'in:active,inactive,pending',
        ]
    ];
    
    /**
     * Custom validation messages
     */
    protected array $messages = [
        'email.required' => 'Email là bắt buộc',
        'email.email' => 'Email không đúng định dạng',
        'email.unique' => 'Email này đã được sử dụng trong hệ thống',
        'password_hash.required' => 'Password hash là bắt buộc',
        'first_name.required' => 'Họ là bắt buộc',
        'first_name.max' => 'Họ không được vượt quá 50 ký tự',
        'last_name.required' => 'Tên là bắt buộc',  
        'last_name.max' => 'Tên không được vượt quá 50 ký tự',
        'phone.regex' => 'Số điện thoại phải có 10-11 chữ số',
        'role.required' => 'Role là bắt buộc',
        'role.in' => 'Role phải là: admin, user, hoặc guest',
        'status.required' => 'Trạng thái là bắt buộc',
        'status.in' => 'Trạng thái phải là: active, inactive, hoặc pending',
    ];

    /**
     * Custom attribute names
     */
    protected array $attributes = [
        'email' => 'Địa chỉ email',
        'first_name' => 'Họ',
        'last_name' => 'Tên',
        'phone' => 'Số điện thoại',
        'role' => 'Vai trò',
        'status' => 'Trạng thái',
    ];
}