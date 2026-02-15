<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8">
                    <h1 class="text-3xl font-bold mb-8">📚 Mes Rapports à corriger</h1>

                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-lg mb-6">
                            ✅ {{ session('success') }}
                        </div>
                    @endif

                    @php $reports = auth()->user()->assignedReports()->with('student')->latest()->get(); @endphp

                    @if ($reports->count() === 0)
                        <div class="text-center py-12 text-gray-500">
                            Aucun rapport affecté pour le moment.
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
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($reports as $report)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4">{{ $report->student->name }}</td>
                                            <td class="px-6 py-4 font-medium">{{ $report->title }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                {{ $report->created_at->format('d/m/Y') }}</td>
                                            <td class="px-6 py-4">
                                                <div class="space-y-2">
                                                    <a href="{{ asset('storage/' . $report->file_path) }}"
                                                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm font-bold block mb-2"
                                                        target="_blank">
                                                        📥 Télécharger PDF
                                                    </a>

                                                    @if ($report->teacher_status !== 'Validé par enseignant')
                                                        <form method="POST"
                                                            action="{{ route('reports.teacher-comment', $report) }}"
                                                            class="inline-block">
                                                            @csrf
                                                            <textarea name="comment" placeholder="Ajouter un commentaire..." class="w-48 p-2 border rounded mb-2 text-xs"
                                                                rows="2"></textarea>
                                                            <br>
                                                            <button type="submit"
                                                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                                                                💬 Commenter & Valider
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span
                                                            class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">
                                                            ✅ Validé
                                                        </span>
                                                    @endif
                                                </div>
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
