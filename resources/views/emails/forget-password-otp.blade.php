@extends('emails.layouts.master')

@section('content')
<p>{{ __('email.forgetPassword.line1') }}</p>

<div class="action">
    <span class="button button-dark">{{ $data['otp'] }}</span>
</div>

<p>{{ __('email.forgetPassword.line2', ['minutes' => config('site.otp.expiration_time_in_minutes')]) }}</p>

@endsection
