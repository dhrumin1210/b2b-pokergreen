@extends('emails.layouts.master')

@section('content')
<p style="font-size: 16px; margin-bottom: 20px;">
    {{ __('email.userApproved.line1') }}
</p>

<p style="font-size: 15px; margin-bottom: 10px;">
    {{ __('email.userApproved.details') }}
</p>
<ul style="font-size: 15px; margin-bottom: 20px;">
    <li><strong>{{ __('email.userApproved.name') }}:</strong> {{ $data['user']->name }}</li>
    <li><strong>{{ __('email.userApproved.email') }}:</strong> {{ $data['user']->email }}</li>
</ul>

<p style="font-size: 15px; margin-bottom: 10px;">
    {{ __('email.userApproved.line2') }}
</p>
<p style="font-size: 15px; margin-bottom: 24px;">
    {{ __('email.userApproved.line3') }}
</p>
{{--
<div style="text-align: center; margin: 24px 0;">
    <a href="{{ url('/login') }}" style="background: #28a745; color: #fff; padding: 12px 28px; border-radius: 5px; text-decoration: none; font-weight: bold;">
{{ __('email.userApproved.button') }}
</a>
</div>
--}}
@endsection
