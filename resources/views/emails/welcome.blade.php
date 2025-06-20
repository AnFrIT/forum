@extends('emails.layout')

@section('content')
<tr>
    <td style="padding: 40px 40px 0 40px;">
        <div style="text-align: center; margin-bottom: 30px;">
            <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" style="height: 50px; width: auto;">
        </div>
        
        <h1 style="color: #1f2937; font-size: 28px; font-weight: 700; text-align: center; margin: 0 0 20px 0; line-height: 1.2;">
            {{ __('Welcome to :app!', ['app' => config('app.name')]) }}
        </h1>
        
        <p style="color: #4b5563; font-size: 16px; line-height: 1.6; margin: 0 0 20px 0; text-align: center;">
            {{ __('Hello :name! Thank you for joining our community.', ['name' => $user->name]) }}
        </p>
    </td>
</tr>

<tr>
    <td style="padding: 0 40px;">
        <div style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border-radius: 12px; padding: 30px; margin: 20px 0; text-align: center;">
            <h2 style="color: white; font-size: 20px; font-weight: 600; margin: 0 0 15px 0;">
                {{ __('Your account is ready!') }}
            </h2>
            <p style="color: rgba(255, 255, 255, 0.9); font-size: 14px; line-height: 1.5; margin: 0 0 20px 0;">
                {{ __('You can now start exploring our forum and participate in discussions.') }}
            </p>
            <a href="{{ route('home') }}" 
               style="display: inline-block; background: rgba(255, 255, 255, 0.2); color: white; text-decoration: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; border: 1px solid rgba(255, 255, 255, 0.3); backdrop-filter: blur(10px);">
                {{ __('Explore Forum') }}
            </a>
        </div>
    </td>
</tr>

<tr>
    <td style="padding: 20px 40px;">
        <div style="background: #f9fafb; border-radius: 12px; padding: 25px;">
            <h3 style="color: #1f2937; font-size: 18px; font-weight: 600; margin: 0 0 15px 0;">
                {{ __('Getting Started') }}
            </h3>
            
            <div style="margin-bottom: 15px;">
                <div style="display: flex; align-items: flex-start; margin-bottom: 12px;">
                    <div style="background: #dbeafe; border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; margin-right: 12px; flex-shrink: 0;">
                        <span style="color: #3b82f6; font-weight: bold; font-size: 12px;">1</span>
                    </div>
                    <div>
                        <strong style="color: #1f2937; font-size: 14px;">{{ __('Complete your profile') }}</strong>
                        <p style="color: #6b7280; font-size: 13px; margin: 5px 0 0 0; line-height: 1.4;">
                            {{ __('Add a profile picture and tell us about yourself.') }}
                        </p>
                    </div>
                </div>
                
                <div style="display: flex; align-items: flex-start; margin-bottom: 12px;">
                    <div style="background: #dcfce7; border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; margin-right: 12px; flex-shrink: 0;">
                        <span style="color: #16a34a; font-weight: bold; font-size: 12px;">2</span>
                    </div>
                    <div>
                        <strong style="color: #1f2937; font-size: 14px;">{{ __('Explore categories') }}</strong>
                        <p style="color: #6b7280; font-size: 13px; margin: 5px 0 0 0; line-height: 1.4;">
                            {{ __('Discover topics that interest you and join the conversation.') }}
                        </p>
                    </div>
                </div>
                
                <div style="display: flex; align-items: flex-start;">
                    <div style="background: #fef3c7; border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; margin-right: 12px; flex-shrink: 0;">
                        <span style="color: #d97706; font-weight: bold; font-size: 12px;">3</span>
                    </div>
                    <div>
                        <strong style="color: #1f2937; font-size: 14px;">{{ __('Start participating') }}</strong>
                        <p style="color: #6b7280; font-size: 13px; margin: 5px 0 0 0; line-height: 1.4;">
                            {{ __('Create your first topic or reply to existing discussions.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </td>
</tr>

<tr>
    <td style="padding: 20px 40px;">
        <div style="text-align: center;">
            <h3 style="color: #1f2937; font-size: 18px; font-weight: 600; margin: 0 0 15px 0;">
                {{ __('Quick Links') }}
            </h3>
            
            <div style="display: flex; justify-content: center; gap: 20px; flex-wrap: wrap;">
                <a href="{{ route('profile.edit') }}" 
                   style="display: inline-block; background: #f3f4f6; color: #374151; text-decoration: none; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 500; border: 1px solid #e5e7eb;">
                    {{ __('Edit Profile') }}
                </a>
                
                <a href="{{ route('categories.index') }}" 
                   style="display: inline-block; background: #f3f4f6; color: #374151; text-decoration: none; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 500; border: 1px solid #e5e7eb;">
                    {{ __('Browse Categories') }}
                </a>
                
                <a href="{{ route('search') }}" 
                   style="display: inline-block; background: #f3f4f6; color: #374151; text-decoration: none; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 500; border: 1px solid #e5e7eb;">
                    {{ __('Search Forum') }}
                </a>
            </div>
        </div>
    </td>
</tr>

<tr>
    <td style="padding: 30px 40px 20px 40px;">
        <div style="border-top: 1px solid #e5e7eb; padding-top: 20px;">
            <p style="color: #6b7280; font-size: 14px; line-height: 1.5; margin: 0 0 15px 0; text-align: center;">
                {{ __('Need help getting started? Our community guidelines and FAQ can help you make the most of your forum experience.') }}
            </p>
            
            <div style="text-align: center;">
                <a href="#" style="color: #3b82f6; text-decoration: none; font-size: 14px; font-weight: 500; margin: 0 15px;">
                    {{ __('Community Guidelines') }}
                </a>
                <a href="#" style="color: #3b82f6; text-decoration: none; font-size: 14px; font-weight: 500; margin: 0 15px;">
                    {{ __('FAQ') }}
                </a>
                <a href="#" style="color: #3b82f6; text-decoration: none; font-size: 14px; font-weight: 500; margin: 0 15px;">
                    {{ __('Contact Support') }}
                </a>
            </div>
        </div>
    </td>
</tr>

<tr>
    <td style="padding: 20px 40px 40px 40px;">
        <div style="background: #f8fafc; border-radius: 8px; padding: 20px; text-align: center; border-left: 4px solid #3b82f6;">
            <p style="color: #4b5563; font-size: 13px; line-height: 1.5; margin: 0;">
                <strong style="color: #1f2937;">{{ __('Account Details:') }}</strong><br>
                {{ __('Email:') }} {{ $user->email }}<br>
                {{ __('Joined:') }} {{ $user->created_at->format('M d, Y') }}<br>
                {{ __('User ID:') }} #{{ $user->id }}
            </p>
        </div>
    </td>
</tr>
@endsection

@section('footer')
<tr>
    <td style="padding: 20px 40px; text-align: center; border-top: 1px solid #e5e7eb;">
        <p style="color: #9ca3af; font-size: 12px; margin: 0 0 10px 0;">
            {{ __('This email was sent to :email because you created an account on :app.', [
                'email' => $user->email,
                'app' => config('app.name')
            ]) }}
        </p>
        
        <p style="color: #9ca3af; font-size: 12px; margin: 0;">
            {{ __('If you have any questions, please contact us at :email.', [
                'email' => config('mail.from.address')
            ]) }}
        </p>
    </td>
</tr>
@endsection