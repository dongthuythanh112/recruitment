@extends('admin.layouts.app')

@section('title', 'Thêm ngành nghề')

@section('content')
    <form method="POST" action="{{ route('admin.industries.store') }}" class="max-w-2xl">
        @csrf
        @include('admin.industries._form')
    </form>
@endsection
