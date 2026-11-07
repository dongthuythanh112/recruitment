@extends('admin.layouts.app')

@section('title', 'Sửa nhà tuyển dụng')

@section('content')
    <form method="POST" action="{{ route('admin.recruiters.update', $recruiter) }}" class="max-w-4xl">
        @csrf
        @method('PUT')
        @include('admin.recruiters._form')
    </form>
@endsection
