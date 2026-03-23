@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-semibold">Мои состаноци</h2>
                        <a href="{{ route('meetings.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            + Нов состанок
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Наслов</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Датум</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Статус</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Акции</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($meetings as $meeting)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $meeting->title }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $meeting->start_time->format('d.m.Y H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($meeting->status === 'completed') bg-green-100 text-green-800
                                    @elseif($meeting->status === 'scheduled') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $meeting->status }}
                                </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('meetings.show', $meeting) }}" class="text-blue-600 hover:text-blue-900 mr-3">Преглед</a>
                                    <a href="{{ route('meetings.edit', $meeting) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Уреди</a>
                                    @if($meeting->latestMinutes)
                                        <a href="{{ route('meetings.minutes.show', ['meeting' => $meeting, 'minutes' => $meeting->latestMinutes->id]) }}"
                                           class="text-green-600 hover:text-green-900">Записник</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
