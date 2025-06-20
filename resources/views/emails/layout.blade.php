<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ in_array(app()->getLocale(), ['ar', 'he', 'fa']) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>@yield('title', config('app.name'))</title>
    
    <style>
        /* Email-safe CSS */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        /* Reset styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #374151;
            background-color: #f9fafb;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        /* RTL Support */
        [dir="rtl"] {
            font-family: 'Noto Sans Arabic', 'Inter', Arial, sans-serif;
        }
        
        table {
            border-collapse: collapse;
            border-spacing: 0;
        }
        
        img {
            border: 0;
            outline: none;
            text-decoration: none;
            -ms-interpolation-mode: bicubic;
            max-width: 100%;
            height: auto;
        }
        
        a {
            color: #3b82f6;
            text-decoration: none;
        }
        
        a:hover {
            color: #2563eb;
            text-decoration: underline;
        }
        
        /* Main container */
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        /* Header styles */
        .email-header {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            padding: 0;
        }
        
        .header-content {
            padding: 30px 40px;
            text-align: center;
        }
        
        .logo {
            max-height: 40px;
            width: auto;
            margin-bottom: 15px;
        }
        
        .header-title {
            color: white;
            font-size: 24px;
            font-weight: 700;
            margin: 0;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }
        
        /* Content styles */
        .email-content {
            background-color: #ffffff;
        }
        
        /* Footer styles */
        .email-footer {
            background-color: #f8fafc;
            border-top: 1px solid #e5e7eb;
        }
        
        .footer-content {
            padding: 30px 40px;
            text-align: center;
        }
        
        .footer-links {
            margin: 20px 0;
        }
        
        .footer-links a {
            color: #6b7280;
            font-size: 14px;
            text-decoration: none;
            margin: 0 15px;
        }
        
        .footer-links a:hover {
            color: #3b82f6;
        }
        
        .social-links {
            margin: 20px 0;
        }
        
        .social-links a {
            display: inline-block;
            margin: 0 8px;
            padding: 8px;
            background-color: #f3f4f6;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            text-align: center;
            line-height: 20px;
        }
        
        .social-links a:hover {
            background-color: #e5e7eb;
        }
        
        /* Responsive */
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
                margin: 0 !important;
                border-radius: 0 !important;
            }
            
            .header-content,
            .footer-content {
                padding: 20px !important;
            }
            
            .header-title {
                font-size: 20px !important;
            }
            
            td[style*="padding: 40px"] {
                padding: 20px !important;
            }
            
            td[style*="padding: 0 40px"] {
                padding: 0 20px !important;
            }
            
            td[style*="padding: 20px 40px"] {
                padding: 15px 20px !important;
            }
            
            td[style*="padding: 30px 40px"] {
                padding: 20px !important;
            }
            
            .footer-links a {
                display: block !important;
                margin: 10px 0 !important;
            }
        }
        
        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            .email-container {
                background-color: #1f2937 !important;
            }
            
            .email-content {
                background-color: #1f2937 !important;
            }
            
            .email-footer {
                background-color: #111827 !important;
                border-top-color: #374151 !important;
            }
            
            body {
                background-color: #111827 !important;
            }
        }
    </style>
</head>
<body>
    <div style="background-color: #f9fafb; padding: 20px 0; min-height: 100vh;">
        <table role="presentation" style="width: 100%; border: 0; cellspacing: 0; cellpadding: 0;">
            <tr>
                <td style="text-align: center;">
                    <!-- Email Container -->
                    <table role="presentation" class="email-container" style="width: 100%; max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); overflow: hidden;">
                        
                        <!-- Header -->
                        <tr>
                            <td class="email-header" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); padding: 0;">
                                <div class="header-content" style="padding: 30px 40px; text-align: center;">
                                    @if(file_exists(public_path('images/logo-white.png')))
                                    <img src="{{ asset('images/logo-white.png') }}" alt="{{ config('app.name') }}" class="logo" style="max-height: 40px; width: auto; margin-bottom: 15px;">
                                    @endif
                                    
                                    <h1 class="header-title" style="color: white; font-size: 24px; font-weight: 700; margin: 0; text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);">
                                        {{ config('app.name') }}
                                    </h1>
                                </div>
                            </td>
                        </tr>
                        
                        <!-- Content -->
                        <tbody class="email-content" style="background-color: #ffffff;">
                            @yield('content')
                        </tbody>
                        
                        <!-- Footer -->
                        <tr>
                            <td class="email-footer" style="background-color: #f8fafc; border-top: 1px solid #e5e7eb;">
                                <div class="footer-content" style="padding: 30px 40px; text-align: center;">
                                    
                                    @hasSection('footer')
                                        @yield('footer')
                                    @else
                                        <!-- Default Footer -->
                                        <div class="footer-links" style="margin: 20px 0;">
                                            <a href="{{ route('home') }}" style="color: #6b7280; font-size: 14px; text-decoration: none; margin: 0 15px;">
                                                {{ __('Home') }}
                                            </a>
                                            <a href="{{ route('profile.edit') }}" style="color: #6b7280; font-size: 14px; text-decoration: none; margin: 0 15px;">
                                                {{ __('Profile') }}
                                            </a>
                                            <a href="#" style="color: #6b7280; font-size: 14px; text-decoration: none; margin: 0 15px;">
                                                {{ __('Help') }}
                                            </a>
                                            <a href="#" style="color: #6b7280; font-size: 14px; text-decoration: none; margin: 0 15px;">
                                                {{ __('Privacy') }}
                                            </a>
                                        </div>
                                        
                                        @if(config('app.social_links'))
                                        <div class="social-links" style="margin: 20px 0;">
                                            @foreach(config('app.social_links', []) as $platform => $url)
                                            <a href="{{ $url }}" style="display: inline-block; margin: 0 8px; padding: 8px; background-color: #f3f4f6; border-radius: 50%; width: 36px; height: 36px; text-align: center; line-height: 20px;">
                                                @switch($platform)
                                                    @case('facebook')
                                                        <span style="color: #1877f2;">f</span>
                                                        @break
                                                    @case('twitter') 
                                                        <span style="color: #1da1f2;">t</span>
                                                        @break
                                                    @case('instagram')
                                                        <span style="color: #e4405f;">i</span>
                                                        @break
                                                    @case('linkedin')
                                                        <span style="color: #0077b5;">in</span>
                                                        @break
                                                    @default
                                                        <span style="color: #6b7280;">{{ substr($platform, 0, 1) }}</span>
                                                @endswitch
                                            </a>
                                            @endforeach
                                        </div>
                                        @endif
                                        
                                        <p style="color: #9ca3af; font-size: 12px; line-height: 1.5; margin: 15px 0 0 0;">
                                            © {{ date('Y') }} {{ config('app.name') }}. {{ __('All rights reserved.') }}<br>
                                            @if(config('app.address'))
                                                {{ config('app.address') }}<br>
                                            @endif
                                            
                                            <a href="#" style="color: #9ca3af; font-size: 12px; margin: 0 10px;">
                                                {{ __('Unsubscribe') }}
                                            </a>
                                            <a href="#" style="color: #9ca3af; font-size: 12px; margin: 0 10px;">
                                                {{ __('Preferences') }}
                                            </a>
                                        </p>
                                    @endif
                                    
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        
        <!-- Spacer -->
        <table role="presentation" style="width: 100%; margin-top: 20px;">
            <tr>
                <td style="text-align: center;">
                    <p style="color: #9ca3af; font-size: 11px; margin: 0;">
                        {{ __('This email was sent by :app', ['app' => config('app.name')]) }}
                    </p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>