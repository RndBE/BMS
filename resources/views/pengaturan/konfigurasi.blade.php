@extends('layouts.app')

@section('page-title', 'Konfigurasi')

@section('content')

{{-- Tab Navigation --}}
<div class="bg-white dark:bg-[#232323] dark:border dark:border-[#2d2d2d] rounded-xl border border-slate-100 shadow-sm overflow-hidden">

    {{-- Tabs --}}
    <div class="flex border-b border-slate-100 dark:border-[#2d2d2d] px-6">
        @foreach(['ruangan' => 'Ruangan', 'sensor' => 'Sensor', 'perangkat' => 'Perangkat'] as $key => $lbl)
            <a href="{{ route('pengaturan.konfigurasi', ['tab' => $key]) }}"
                class="relative px-4 py-3.5 text-[13.5px] font-medium transition-colors duration-150 no-underline
                    {{ $tab === $key
                        ? 'text-red-700 border-b-2 border-red-700'
                        : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200' }}">
                {{ $lbl }}
            </a>
        @endforeach
    </div>

    {{-- Tab Content --}}
    <div class="p-6">
        @if($tab === 'ruangan')
            @include('pengaturan.tabs.ruangan')

        @elseif($tab === 'sensor')
            @include('pengaturan.tabs.sensor')

        @elseif($tab === 'perangkat')
            @include('pengaturan.tabs.perangkat')

        @endif
    </div>

</div>

@endsection