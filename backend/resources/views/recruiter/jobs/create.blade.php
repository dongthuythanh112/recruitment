@extends('client.layouts.app')

@section('title', 'Tạo tin tuyển dụng')
@section('description', 'Tạo tin tuyển dụng mới cho công ty của bạn.')

@section('content')
    <section class="bg-board px-4 pb-14 text-paper">
        <div class="mx-auto max-w-4xl">
            <div class="border-b border-white/15 py-8 sm:py-10">
                <a href="{{ route('recruiter.jobs.index') }}" class="text-sm text-paper/60 underline underline-offset-4 hover:text-flap">← Quay lại danh sách tin</a>
                <p class="mt-5 text-xs font-semibold uppercase tracking-[0.18em] text-flap">{{ $company->name }}</p>
                <h1 class="mt-1 font-signage text-4xl font-extrabold tracking-wide uppercase sm:text-5xl">Tạo tin tuyển dụng</h1>
            </div>

            <form method="POST" action="{{ route('recruiter.jobs.store') }}" class="mt-6">
                @csrf
                @include('recruiter.jobs._form')
            </form>
        </div>
    </section>
@endsection
