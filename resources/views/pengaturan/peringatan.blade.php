@extends('layouts.app')

@section('page-title', 'Peringatan')

@section('content')

    {{-- Outer Card --}}
    <div class="bg-white dark:bg-[#232323] dark:border dark:border-[#2d2d2d] rounded-2xl shadow-sm border border-slate-200 overflow-hidden">

        {{-- Tab Navigation (inside card) --}}
        <div class="flex gap-0 border-b border-slate-200 dark:border-[#2d2d2d] px-1">
            @php
                $tabs = [
                    'batas-normal'      => 'Batas Normal',
                    'aturan-peringatan' => 'Aturan Peringatan',
                ];
            @endphp
            @foreach($tabs as $key => $label)
                <a href="{{ route('pengaturan.peringatan', ['tab' => $key]) }}"
                   class="relative px-5 py-3.5 text-[13.5px] font-medium no-underline transition-colors
                          {{ $tab === $key
                              ? 'text-red-600 border-b-2 border-red-600 -mb-px'
                              : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- Tab Content --}}
        <div class="p-6">
            @if($tab === 'batas-normal')
                @include('pengaturan.peringatan.batas-normal', ['limits' => $limits])
            @elseif($tab === 'aturan-peringatan')
                @include('pengaturan.peringatan.aturan-peringatan', ['rules' => $rules, 'rooms' => $rooms])
            @endif
        </div>

    </div>

@endsection
