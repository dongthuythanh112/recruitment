@extends('admin.layouts.app')

@section('title', 'Tạo tin tuyển dụng')

@section('content')
    <form method="POST" action="{{ route('admin.jobs.store') }}" class="max-w-4xl">
        @csrf
        @include('admin.jobs._form')
    </form>
@endsection
