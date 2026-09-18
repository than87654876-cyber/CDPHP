@extends('layouts.admin')

@section('title', 'Quản lý gói đăng ký dài hạn - FOODDAILY Admin')

@section('content')
<div 
    id="admin-subscriptions-root" 
    data-react-component="AdminSubscriptionsApp" 
    data-props="{{ json_encode([
        'subscriptions' => $subscriptions ?? [],
        'csrfToken' => csrf_token(),
        'routes' => [
            'subscriptions' => route('quanly_goidangky'),
        ]
    ]) }}"
></div>
@endsection
