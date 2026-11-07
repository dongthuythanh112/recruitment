@extends('admin.layouts.app')

@section('title', 'Thêm ứng viên')

@section('content')
    <form method="POST" action="{{ route('admin.candidates.store') }}" class="max-w-4xl">
        @csrf
        @include('admin.candidates._form')
    </form>
@endsection
