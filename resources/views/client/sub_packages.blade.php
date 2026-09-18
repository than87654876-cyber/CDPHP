@extends('layouts.app')

@section('title', 'Gói Dịch Vụ Của Tôi - FOODDAILY')

@section('content')
<div 
    id="subscription-root" 
    data-react-component="SubscriptionApp" 
    data-props="{{ json_encode([
        'subscriptions' => $subscriptions ?? [],
        'csrfToken' => csrf_token(),
        'routes' => [
            'shop' => route('trangchu'),
        ]
    ]) }}"
></div>
@endsection
