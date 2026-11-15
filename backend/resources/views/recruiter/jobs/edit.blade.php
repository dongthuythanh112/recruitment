@extends('client.layouts.app')

@section('title', 'Chỉnh sửa tin tuyển dụng')
@section('description', 'Chỉnh sửa tin tuyển dụng của công ty bạn.')

@section('content')
    <section class="bg-board px-4 pb-14 text-paper">
        <div class="mx-auto max-w-4xl">
            <div class="border-b border-white/15 py-8 sm:py-10">
                <a href="{{ route('recruiter.jobs.index') }}" class="text-sm text-paper/60 underline underline-offset-4 hover:text-flap">← Quay lại danh sách tin</a>
                <p class="mt-5 text-xs font-semibold uppercase tracking-[0.18em] text-flap">{{ $company->name }}</p>
                <h1 class="mt-1 break-words font-signage text-4xl font-extrabold tracking-wide uppercase sm:text-5xl">Chỉnh sửa tin</h1>
                <p class="mt-2 truncate text-sm text-paper/60">{{ $job->title }}</p>
            </div>

            <form method="POST" action="{{ route('recruiter.jobs.update', $job) }}" class="mt-6">
                @csrf
                @method('PUT')
                @include('recruiter.jobs._form')
            </form>
        </div>
    </section>
@endsection
