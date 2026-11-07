@extends('admin.layouts.app')

@section('title', 'Sửa ngành nghề')

@section('content')
    <form method="POST" action="{{ route('admin.industries.update', $industry) }}" class="max-w-2xl">
        @csrf
        @method('PUT')
        @include('admin.industries._form')
    </form>
@endsection
