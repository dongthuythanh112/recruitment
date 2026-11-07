@extends('admin.layouts.app')

@section('title', 'Thêm nhà tuyển dụng')

@section('content')
    <form method="POST" action="{{ route('admin.recruiters.store') }}" class="max-w-4xl">
        @csrf
        @include('admin.recruiters._form')
    </form>
@endsection
