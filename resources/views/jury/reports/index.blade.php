<x-app-layout>
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-8">
                <h1 class="text-3xl font-bold mb-8">👩‍⚖️ Évaluation Rapports (Jury Cameroun)</h1>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-lg mb-6">
                        ✅ {{ session('success') }}
                    </div>
                @endif

                @php $reports = auth()->user()->juryReports()->with('student', 'teacher')->latest()->get(); @endphp

                @if($reports->count() === 0)
                    <div class="text-center py-12 text-gray-500">
                        <p class="text-2xl mb-4">📭 Aucun rapport à évaluer</p>
                        <p>Les rapports seront affectés par l'administrateur après validation enseignant.</p>
                    </div>
                @else
                    @foreach($reports as $report)
                    <div class="bg-gradient-to-r from-purple-50 to-indigo-50 p-8 mb-8 rounded-xl border-l-8 border-purple-500 shadow-lg">
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $report->title }}</h3>
                                <p class="text-lg text-gray-600">Étudiant : {{ $report->student->name ?? 'N/A' }}</p>
                                <p class="text-sm text-gray-500">Enseignant : {{ $report->teacher->name ?? 'N/A' }}</p>
                            </div>
                            <a href="{{ asset('storage/' . $report->file_path) }}"
                               class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-bold inline-flex items-center"
                               target="_blank">
                                📥 Télécharger PDF
                            </a>
                        </div>

                        <div class="bg-blue-50 p-6 rounded-lg mb-6">
                            <h4 class="font-bold text-lg mb-3 text-blue-900">💬 Commentaire Enseignant</h4>
                            <blockquote class="text-gray-800 italic p-4 bg-white rounded border-l-4 border-blue-400">
                                "{{ $report->teacher_comment ?? 'Aucun commentaire' }}"
                            </blockquote>
                        </div>

                        <form method="POST" action="{{ route('reports.jury-evaluate', $report) }}" class="space-y-6">
                            @csrf

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold mb-2">📝 Note Forme (sur 20)</label>
                                    <input type="number" name="jury_note_forme" min="0" max="20" step="0.5" step="0.5"
                                           class="w-full p-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-purple-200 focus:border-purple-500 transition-all" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold mb-2">📚 Note Fond (sur 20)</label>
                                    <input type="number" name="jury_note_fond" min="0" max="20" step="0.5"
                                           class="w-full p-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-purple-200 focus:border-purple-500 transition-all" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold mb-2">💬 Note Langage (sur 20)</label>
                                    <input type="number" name="jury_note_langage" min="0" max="20" step="0.5"
                                           class="w-full p-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-purple-200 focus:border-purple-500 transition-all" required>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold mb-2">📝 Commentaire Jury</label>
                                    <textarea name="jury_commentaire" rows="4"
                                              class="w-full p-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-purple-200 focus:border-purple-500 transition-all"
                                              placeholder="Commentaire détaillé sur la qualité du rapport..."></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold mb-2">✅ Décision finale</label>
                                    <select name="jury_decision" class="w-full p-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-purple-200 focus:border-purple-500" required>
                                        <option value="">Sélectionner décision</option>
                                        <option value="Validé">✅ Validé</option>
                                        <option value="Rejeté">❌ Rejeté</option>
                                        <option value="À revoir">⚠️ À revoir</option>
                                    </select>
                                </div>
                            </div>

                            <div class="text-center pt-6 border-t border-gray-200">
                                <button type="submit"
                                        class="bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white px-12 py-4 rounded-xl font-bold text-xl shadow-lg transform hover:scale-105 transition-all">
                                    🎓 FINALISER ÉVALUATION
                                </button>
                            </div>
                        </form>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
</x-app-layout>
