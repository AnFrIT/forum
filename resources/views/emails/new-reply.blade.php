@extends('emails.layout')

@section('title', __('New Reply in Topic: :title', ['title' => $topic->title]))

@section('content')
<tr>
    <td style="padding: 40px 40px 0 40px;">
        <div style="text-align: center; margin-bottom: 30px;">
            <div style="background: #dbeafe; border-radius: 50%; width: 60px; height: 60px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                <svg style="width: 24px; height: 24px; color: #3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>
        </div>
        
        <h1 style="color: #1f2937; font-size: 24px; font-weight: 700; text-align: center; margin: 0 0 20px 0; line-height: 1.2;">
            {{ __('New Reply in Your Topic') }}
        </h1>
        
        <p style="color: #4b5563; font-size: 16px; line-height: 1.6; margin: 0 0 20px 0; text-align: center;">
            {{ __('Hello :name! Someone replied to a topic you\'re following.', ['name' => $user->name]) }}
        </p>
    </td>
</tr>

<tr>
    <td style="padding: 0 40px;">
        <div style="background: #f8fafc; border-radius: 12px; padding: 25px; margin: 20px 0; border-left: 4px solid #3b82f6;">
            <h2 style="color: #1f2937; font-size: 18px; font-weight: 600; margin: 0 0 10px 0;">
                {{ $topic->title }}
            </h2>
            
            <div style="display: flex; align-items: center; margin-bottom: 15px; flex-wrap: wrap; gap: 15px;">
                <div style="display: flex; align-items: center;">
                    <div style="background: #e0e7ff; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; margin-right: 8px;">
                        <svg style="width: 10px; height: 10px; color: #5b21b6;" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <span style="color: #6b7280; font-size: 13px;">{{ $post->user->name }}</span>
                </div>
                
                <div style="display: flex; align-items: center;">
                    <div style="background: #ddd6fe; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; margin-right: 8px;">
                        <svg style="width: 10px; height: 10px; color: #7c3aed;" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <span style="color: #6b7280; font-size: 13px;">{{ $post->created_at->diffForHumans() }}</span>
                </div>
                
                <div style="display: flex; align-items: center;">
                    <div style="background: #fef3c7; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; margin-right: 8px;">
                        <svg style="width: 10px; height: 10px; color: #d97706;" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"/>
                        </svg>
                    </div>
                    <span style="color: #6b7280; font-size: 13px;">{{ $topic->category->name }}</span>
                </div>
            </div>
        </div>
    </td>
</tr>

<tr>
    <td style="padding: 0 40px;">
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 25px; margin: 20px 0; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
            <div style="display: flex; align-items: flex-start; margin-bottom: 15px;">
                @if($post->user->avatar)
                <img src="{{ $post->user->avatar_url }}" alt="{{ $post->user->name }}" style="width: 40px; height: 40px; border-radius: 50%; margin-right: 15px; object-fit: cover;">
                @else
                <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #3b82f6, #8b5cf6); display: flex; align-items: center; justify-content: center; margin-right: 15px; color: white; font-weight: 600; font-size: 16px;">
                    {{ substr($post->user->name, 0, 1) }}
                </div>
                @endif
                
                <div style="flex-grow: 1;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
                        <h3 style="color: #1f2937; font-size: 16px; font-weight: 600; margin: 0;">
                            {{ $post->user->name }}
                            @if($post->user->is_moderator)
                                <span style="background: #ddd6fe; color: #5b21b6; font-size: 10px; font-weight: 600; padding: 2px 6px; border-radius: 4px; margin-left: 8px;">MOD</span>
                            @endif
                            @if($post->user->is_admin)
                                <span style="background: #fecaca; color: #dc2626; font-size: 10px; font-weight: 600; padding: 2px 6px; border-radius: 4px; margin-left: 8px;">ADMIN</span>
                            @endif
                        </h3>
                        <span style="color: #9ca3af; font-size: 12px;">
                            {{ $post->created_at->format('M d, Y \a\t H:i') }}
                        </span>
                    </div>
                    
                    <div style="color: #374151; font-size: 14px; line-height: 1.6;">
                        {!! nl2br(e(Str::limit($post->content, 300))) !!}
                        @if(strlen($post->content) > 300)
                            <p style="margin: 10px 0 0 0;">
                                <em style="color: #6b7280; font-size: 13px;">{{ __('...and more') }}</em>
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </td>
</tr>

<tr>
    <td style="padding: 20px 40px;">
        <div style="text-align: center;">
            <a href="{{ route('topics.show', $topic) }}" 
               style="display: inline-block; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; text-decoration: none; padding: 16px 32px; border-radius: 10px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3); text-align: center; min-width: 200px;">
                {{ __('View Full Topic') }}
            </a>
            
            <p style="color: #6b7280; font-size: 13px; line-height: 1.5; margin: 15px 0 0 0;">
                {{ __('Join the conversation and share your thoughts') }}
            </p>
        </div>
    </td>
</tr>

<tr>
    <td style="padding: 20px 40px;">
        <div style="background: #f9fafb; border-radius: 12px; padding: 20px;">
            <h3 style="color: #1f2937; font-size: 16px; font-weight: 600; margin: 0 0 12px 0;">
                {{ __('Topic Statistics') }}
            </h3>
            
            <div style="display: flex; justify-content: space-around; text-align: center; flex-wrap: wrap; gap: 20px;">
                <div>
                    <div style="color: #3b82f6; font-size: 24px; font-weight: 700;">{{ $topic->posts_count ?? 0 }}</div>
                    <div style="color: #6b7280; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('Replies') }}</div>
                </div>
                <div>
                    <div style="color: #10b981; font-size: 24px; font-weight: 700;">{{ $topic->views ?? 0 }}</div>
                    <div style="color: #6b7280; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('Views') }}</div>
                </div>
                <div>
                    <div style="color: #f59e0b; font-size: 24px; font-weight: 700;">{{ $topic->followers_count ?? 0 }}</div>
                    <div style="color: #6b7280; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('Followers') }}</div>
                </div>
            </div>
        </div>
    </td>
</tr>

<tr>
    <td style="padding: 20px 40px;">
        <div style="background: #eff6ff; border-radius: 12px; padding: 20px; border-left: 4px solid #3b82f6;">
            <h3 style="color: #1e40af; font-size: 16px; font-weight: 600; margin: 0 0 12px 0;">
                {{ __('Notification Settings') }}
            </h3>
            
            <p style="color: #1e40af; font-size: 14px; line-height: 1.5; margin: 0 0 15px 0;">
                {{ __('You\'re receiving this email because you\'re following this topic.') }}
            </p>
            
            <div style="text-align: center;">
                <a href="{{ route('topics.unfollow', $topic) }}" 
                   style="color: #6b7280; text-decoration: none; font-size: 13px; font-weight: 500; padding: 8px 16px; border: 1px solid #d1d5db; border-radius: 6px; background: white;">
                    {{ __('Unfollow Topic') }}
                </a>
                <a href="{{ route('profile.edit') }}#notifications" 
                   style="color: #6b7280; text-decoration: none; font-size: 13px; font-weight: 500; padding: 8px 16px; border: 1px solid #d1d5db; border-radius: 6px; background: white; margin-left: 10px;">
                    {{ __('Email Preferences') }}
                </a>
            </div>
        </div>
    </td>
</tr>
@endsection

@section('footer')
<tr>
    <td style="padding: 20px 40px; text-align: center; border-top: 1px solid #e5e7eb;">
        <p style="color: #9ca3af; font-size: 12px; margin: 0 0 10px 0;">
            {{ __('This notification was sent to :email because you\'re following the topic ":title".', [
                'email' => $user->email,
                'title' => Str::limit($topic->title, 50)
            ]) }}
        </p>
        
        <p style="color: #9ca3af; font-size: 12px; margin: 0;">
            <a href="{{ route('topics.unfollow', $topic) }}" style="color: #9ca3af; font-size: 12px;">
                {{ __('Unsubscribe from this topic') }}
            </a>
            {{ __(' or ') }}
            <a href="{{ route('profile.edit') }}#notifications" style="color: #9ca3af; font-size: 12px;">
                {{ __('manage all notifications') }}
            </a>
        </p>
    </td>
</tr>
@endsection