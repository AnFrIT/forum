@extends('emails.layout')

@section('title', __('Reset Your Password'))

@section('content')
<tr>
    <td style="padding: 40px 40px 0 40px;">
        <div style="text-align: center; margin-bottom: 30px;">
            <div style="background: #fef2f2; border-radius: 50%; width: 60px; height: 60px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                <svg style="width: 24px; height: 24px; color: #ef4444;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
        </div>
        
        <h1 style="color: #1f2937; font-size: 28px; font-weight: 700; text-align: center; margin: 0 0 20px 0; line-height: 1.2;">
            {{ __('Reset Your Password') }}
        </h1>
        
        <p style="color: #4b5563; font-size: 16px; line-height: 1.6; margin: 0 0 20px 0; text-align: center;">
            {{ __('Hello :name! We received a request to reset your password.', ['name' => $user->name]) }}
        </p>
    </td>
</tr>

<tr>
    <td style="padding: 0 40px;">
        <div style="background: #fef9e7; border-left: 4px solid #f59e0b; border-radius: 8px; padding: 20px; margin: 20px 0;">
            <div style="display: flex; align-items: flex-start;">
                <div style="background: #fbbf24; border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; margin-right: 12px; flex-shrink: 0;">
                    <svg style="width: 12px; height: 12px; color: white;" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <h3 style="color: #92400e; font-size: 16px; font-weight: 600; margin: 0 0 8px 0;">
                        {{ __('Security Notice') }}
                    </h3>
                    <p style="color: #92400e; font-size: 14px; line-height: 1.5; margin: 0;">
                        {{ __('If you didn\'t request this password reset, please ignore this email. Your password will remain unchanged.') }}
                    </p>
                </div>
            </div>
        </div>
    </td>
</tr>

<tr>
    <td style="padding: 20px 40px;">
        <div style="text-align: center;">
            <p style="color: #4b5563; font-size: 16px; line-height: 1.6; margin: 0 0 25px 0;">
                {{ __('Click the button below to choose a new password:') }}
            </p>
            
            <a href="{{ $resetUrl }}" 
               style="display: inline-block; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; text-decoration: none; padding: 16px 32px; border-radius: 10px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3); text-align: center; min-width: 200px;">
                {{ __('Reset Password') }}
            </a>
            
            <p style="color: #6b7280; font-size: 13px; line-height: 1.5; margin: 20px 0 0 0;">
                {{ __('This link will expire in :count minutes.', ['count' => config('auth.passwords.users.expire', 60)]) }}
            </p>
        </div>
    </td>
</tr>

<tr>
    <td style="padding: 20px 40px;">
        <div style="background: #f3f4f6; border-radius: 12px; padding: 25px;">
            <h3 style="color: #1f2937; font-size: 18px; font-weight: 600; margin: 0 0 15px 0;">
                {{ __('Trouble clicking the button?') }}
            </h3>
            
            <p style="color: #4b5563; font-size: 14px; line-height: 1.5; margin: 0 0 15px 0;">
                {{ __('Copy and paste this URL into your browser:') }}
            </p>
            
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px; word-break: break-all;">
                <code style="color: #1f2937; font-size: 13px; font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;">
                    {{ $resetUrl }}
                </code>
            </div>
        </div>
    </td>
</tr>

<tr>
    <td style="padding: 20px 40px;">
        <div style="background: #eff6ff; border-radius: 12px; padding: 25px; border-left: 4px solid #3b82f6;">
            <h3 style="color: #1e40af; font-size: 18px; font-weight: 600; margin: 0 0 15px 0;">
                {{ __('Security Tips') }}
            </h3>
            
            <ul style="color: #1e40af; font-size: 14px; line-height: 1.6; margin: 0; padding-left: 20px;">
                <li style="margin-bottom: 8px;">{{ __('Use a strong, unique password that you haven\'t used elsewhere') }}</li>
                <li style="margin-bottom: 8px;">{{ __('Include a mix of letters, numbers, and special characters') }}</li>
                <li style="margin-bottom: 8px;">{{ __('Consider using a password manager') }}</li>
                <li>{{ __('Enable two-factor authentication if available') }}</li>
            </ul>
        </div>
    </td>
</tr>

<tr>
    <td style="padding: 30px 40px 20px 40px;">
        <div style="border-top: 1px solid #e5e7eb; padding-top: 20px; text-align: center;">
            <p style="color: #6b7280; font-size: 14px; line-height: 1.5; margin: 0 0 15px 0;">
                {{ __('For your security, we recommend that you:') }}
            </p>
            
            <div style="background: #f9fafb; border-radius: 8px; padding: 15px; margin: 15px 0;">
                <p style="color: #4b5563; font-size: 13px; line-height: 1.5; margin: 0;">
                    <strong style="color: #1f2937;">{{ __('Request Details:') }}</strong><br>
                    {{ __('Time:') }} {{ now()->format('M d, Y \a\t H:i T') }}<br>
                    {{ __('IP Address:') }} {{ request()->ip() }}<br>
                    {{ __('Browser:') }} {{ request()->userAgent() }}
                </p>
            </div>
        </div>
    </td>
</tr>
@endsection

@section('footer')
<tr>
    <td style="padding: 20px 40px; text-align: center; border-top: 1px solid #e5e7eb;">
        <p style="color: #9ca3af; font-size: 12px; margin: 0 0 10px 0;">
            {{ __('This password reset email was sent to :email from :app.', [
                'email' => $user->email,
                'app' => config('app.name')
            ]) }}
        </p>
        
        <p style="color: #9ca3af; font-size: 12px; margin: 0;">
            {{ __('If you didn\'t request this, please contact us immediately at :email.', [
                'email' => config('mail.from.address')
            ]) }}
        </p>
    </td>
</tr>
@endsection