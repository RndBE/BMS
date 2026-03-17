@extends('layouts.app')

@section('page-title', 'Pengguna')

@section('content')

    {{-- Outer Card --}}
    <div class="bg-white dark:bg-[#232323] dark:border dark:border-[#2d2d2d] rounded-2xl shadow-sm border border-slate-200 overflow-hidden">

        {{-- Tab Navigation (inside card) --}}
        <div class="flex gap-0 border-b border-slate-200 dark:border-[#2d2d2d] px-1">
            @php
                $tabs = [
                    'user'       => 'User',
                    'role'       => 'Role',
                    'permission' => 'Permission',
                ];
            @endphp
            @foreach($tabs as $key => $label)
                <a href="{{ route('pengaturan.pengguna', ['tab' => $key]) }}"
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
            @if($tab === 'user')
                @include('pengaturan.pengguna.tab-user', ['users' => $users, 'allRoles' => $allRoles, 'search' => $search])
            @elseif($tab === 'role')
                @include('pengaturan.pengguna.tab-role', ['roles' => $roles, 'allPerms' => $allPerms, 'search' => $search])
            @elseif($tab === 'permission')
                @include('pengaturan.pengguna.tab-permission', ['permissions' => $permissions, 'search' => $search])
            @endif
        </div>

    </div>

</div>
@endsection
