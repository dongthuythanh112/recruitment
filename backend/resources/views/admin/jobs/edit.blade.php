@extends('admin.layouts.app')

@section('title', 'Sửa tin tuyển dụng')

@section('content')
    <form method="POST" action="{{ route('admin.jobs.update', $job) }}" class="max-w-4xl">
        @csrf
        @method('PUT')
        @include('admin.jobs._form')
    </form>
@endsection
