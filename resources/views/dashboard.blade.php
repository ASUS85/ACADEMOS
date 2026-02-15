<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if (session('success'))
                        <div
                            class="bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-lg mb-6 animate-pulse">
                            <strong>✅ {{ session('success') }}</strong>
                        </div>
                    @endif

                    {{-- MESSAGES D'ERREUR --}}
                    @if (session('error'))
                        <div
                            class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-lg mb-6 animate-pulse">
                            <strong>❌ {{ session('error') }}</strong>
                        </div>
                    @endif
                    <h1 class="text-3xl font-bold mb-6">🎓 ACADEMOS Dashboard</h1>

                    {{-- AFFICHAGE DU RÔLE --}}
                    <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-6">
                        <strong>👤 Bonjour {{ auth()->user()->name }} !</strong><br>
                        <span class="text-lg">🎭 Ton rôle : <span
                                class="font-bold text-blue-800">{{ auth()->user()->roles->first()->name ?? 'Aucun rôle' }}</span></span>
                    </div>

                    {{-- LIENS SELON RÔLE --}}
                    @if (auth()->user()->hasRole('admin'))
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <a href="/admin/reports"
                                class="bg-green-500 hover:bg-green-600 text-white p-6 rounded-lg text-center">
                                📋 Tous les rapports<br><span class="text-sm">Gérer / Affecter</span>
                            </a>
                            <a href="/admin/users"
                                class="bg-blue-500 hover:bg-blue-600 text-white p-6 rounded-lg text-center">
                                👥 Gérer utilisateurs
                            </a>
                            <a href="/admin/stats"
                                class="bg-purple-500 hover:bg-purple-600 text-white p-6 rounded-lg text-center">
                                📊 Statistiques
                            </a>
                        </div>
                    @elseif(auth()->user()->hasRole('jury'))
                        <div class="bg-purple-100 border border-purple-400 text-purple-800 px-6 py-4 rounded-lg">
                            <h2 class="text-2xl font-bold mb-4">👩‍⚖️ Mes Rapports à évaluer</h2>
                            <p class="mb-4">{{ auth()->user()->juryReports()->count() }} rapport(s) à évaluer</p>
                            <a href="/jury/reports"
                                class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-bold">
                                📊 Évaluer rapports
                            </a>
                        </div>
                    @elseif(auth()->user()->hasRole('student'))
                        <div class="bg-yellow-100 border border-yellow-400 text-yellow-800 px-6 py-4 rounded-lg">
                            <h2 class="text-2xl font-bold mb-4">📄 Mes Rapports de Stage</h2>
                            <a href="/student/reports/create"
                                class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-3 rounded-lg font-bold">
                                ➕ Soumettre un nouveau rapport
                            </a>

                        </div>
                    @elseif(auth()->user()->hasRole('teacher'))
                        <div class="bg-orange-100 border border-orange-400 text-orange-800 px-6 py-4 rounded-lg">
                            <h2 class="text-2xl font-bold mb-4">📚 Mes Rapports à corriger</h2>
                            <p class="mb-4">{{ auth()->user()->assignedReports()->count() }} rapport(s) affecté(s)</p>
                            <a href="/teacher/reports"
                                class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-lg font-bold">
                                👀 Voir mes rapports
                            </a>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <h2 class="text-xl mb-4">⚠️ Aucun rôle assigné</h2>
                            <p>Contacte l'administrateur</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
