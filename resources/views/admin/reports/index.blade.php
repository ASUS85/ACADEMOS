<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8">
                    <h1 class="text-3xl font-bold mb-8">📊 Tous les rapports (Admin)</h1>

                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-lg mb-6">
                            ✅ {{ session('success') }}
                        </div>
                    @endif

                    @if (App\Models\Report::count() === 0)
                        <div class="text-center py-12 text-gray-500">
                            Aucun rapport soumis pour le moment.
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border border-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Étudiant</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Titre</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Statut</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-xs font-medium text-gray-500 uppercase">
                                            Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Commentaire Enseignant</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Action Jury</th>

                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($reports as $report)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4">{{ $report->student->name }}</td>
                                            <td class="px-6 py-4 font-medium">{{ $report->title }}</td>
                                            <td class="px-6 py-4">
                                                <span
                                                    class="px-3 py-1 rounded-full text-xs font-semibold
                                            @if ($report->status === 'Soumis') bg-yellow-100 text-yellow-800
                                            @elseif($report->status === 'Affecté') bg-blue-100 text-blue-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                                    {{ $report->status }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                {{ $report->created_at->format('d/m/Y') }}</td>
                                            <td class="px-6 py-4 pr-0">
                                                @if ($report->teacher_status === 'Validé par enseignant')
                                                    <div class="bg-blue-50 p-3 rounded border-l-4 border-blue-400">
                                                        <p class="text-sm text-gray-800 mb-1">
                                                            "{{ Str::limit($report->teacher_comment, 80) }}"</p>
                                                        <p class="text-xs text-blue-600 font-medium">
                                                            {{ $report->teacher->name ?? 'Enseignant' }}</p>
                                                    </div>
                                                @else
                                                    <span class="text-gray-400 text-sm italic">En attente
                                                        enseignant</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4">
                                                @if ($report->teacher_status === 'Validé par enseignant' && !$report->jury_id)
                                                    <form method="POST"
                                                        action="{{ route('reports.assign-jury', $report) }}"
                                                        class="inline">
                                                        @csrf
                                                        <select name="jury_id"
                                                            class="border rounded px-3 py-1 mr-2 text-sm" required>
                                                            @foreach (\App\Models\User::role('jury')->get() as $jury)
                                                                <option value="{{ $jury->id }}">{{ $jury->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <button type="submit"
                                                            class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded text-sm font-bold">
                                                            👩‍⚖️ Affecter Jury
                                                        </button>
                                                    </form>
                                                @elseif($report->jury_id)
                                                    <span
                                                        class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">
                                                        👩‍⚖️ Jury assigné
                                                    </span>
                                                @else
                                                    <span class="text-gray-500 text-sm">En attente validation
                                                        enseignant</span>
                                                @endif
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
