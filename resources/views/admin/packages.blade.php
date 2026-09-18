@extends('layouts.admin')

@section('title', 'Gói dịch vụ - FOODDAILY Admin')

@section('content')
<div 
    id="admin-packages-root" 
    data-react-component="AdminPackagesApp" 
    data-props="{{ json_encode([
        'packages' => $packages ?? [],
        'csrfToken' => csrf_token(),
        'routes' => [
            'createPackage' => route('goidichvu_them'),
        ]
    ]) }}"
></div>
@endsection
