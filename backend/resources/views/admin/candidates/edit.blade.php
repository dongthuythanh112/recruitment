@extends('admin.layouts.app')

@section('title', 'Sửa ứng viên')

@section('content')
    <form method="POST" action="{{ route('admin.candidates.update', $candidate) }}" class="max-w-4xl">
        @csrf
        @method('PUT')
        @include('admin.candidates._form')
    </form>
@endsection
