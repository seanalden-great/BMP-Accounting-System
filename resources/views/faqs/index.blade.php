<!-- resources/views/faqs/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10 max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                <i data-feather="help-circle" class="w-7 h-7 text-indigo-500"></i> Pusat Bantuan (FAQ)
            </h1>
            <p class="text-slate-500 mt-1">Cari jawaban cepat untuk kendala sistem dan operasional</p>
        </div>
        <button onclick="document.getElementById('faqModal').classList.remove('hidden')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2">
            <i data-feather="plus" class="w-4 h-4"></i> Tambah FAQ
        </button>
    </div>

    <!-- Konten FAQ -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        
        <!-- Live Search Simulation (Optional, via JS below) -->
        <div class="p-4 border-b border-slate-200 bg-slate-50">
            <div class="relative w-full">
                <i data-feather="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" id="searchInput" onkeyup="filterFAQ()" placeholder="Ketik kata kunci pertanyaan atau jawaban..." class="w-full pl-9 pr-4 py-3 border border-slate-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <div class="p-6">
            @if($faqs->isEmpty())
                <div class="text-center text-slate-500 py-10">Tidak ada FAQ yang tersedia.</div>
            @else
                <div class="space-y-3" id="faqList">
                    @foreach($faqs as$faq)
                    <div class="faq-item border border-slate-200 rounded-lg transition-colors overflow-hidden bg-white">
                        <button onclick="toggleFaq({{ $faq->id }})" class="w-full flex items-center justify-between p-4 text-left focus:outline-none hover:bg-slate-50">
                            <div>
                                <span class="text-xs font-bold text-indigo-600 bg-indigo-100 px-2 py-0.5 rounded mr-3 cat-label">{{ $faq->category }}</span>
                                <span class="font-semibold text-slate-800 q-text">{{ $faq->question }}</span>
                            </div>
                            <i data-feather="chevron-down" id="icon-{{ $faq->id }}" class="w-4 h-4 text-slate-400 transition-transform"></i>
                        </button>
                        
                        <div id="answer-{{ $faq->id }}" class="hidden p-4 pt-0 text-slate-600 text-sm border-t border-indigo-100 mt-2 relative">
                            <div class="pt-3 whitespace-pre-wrap pr-10 a-text">{{ $faq->answer }}</div>
                            <form action="{{ route('faqs.destroy', $faq->id) }}" method="POST" onsubmit="return confirm('Hapus pertanyaan ini dari pusat bantuan?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="absolute right-4 top-4 text-slate-400 hover:text-red-500 p-1" title="Hapus FAQ">
                                    <i data-feather="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Tambah FAQ -->
<div id="faqModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col">
        <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
            <h3 class="text-xl font-bold text-slate-800">Buat FAQ Baru</h3>
            <button onclick="document.getElementById('faqModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700">
                <i data-feather="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <form action="{{ route('faqs.store') }}" method="POST" class="p-6 space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Kategori</label>
                <select name="category" required class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="Umum">Umum</option>
                    <option value="Akuntansi">Akuntansi & Keuangan</option>
                    <option value="Inventori">Inventori Gudang</option>
                    <option value="Teknis Sistem">Teknis Sistem</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Pertanyaan</label>
                <input type="text" name="question" required placeholder="Cth: Bagaimana cara mencatat barang retur?" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-indigo-500 font-medium">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Jawaban Lengkap</label>
                <textarea name="answer" required rows="5" placeholder="Tuliskan solusi atau langkah-langkah secara detail..." class="w-full border border-slate-300 rounded-lg p-3 outline-none focus:ring-2 focus:ring-indigo-500 resize-none text-sm leading-relaxed"></textarea>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('faqModal').classList.add('hidden')" class="px-5 py-2.5 text-slate-600 hover:bg-slate-100 rounded-lg font-bold">Batal</button>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg font-bold">Publikasikan FAQ</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleFaq(id) {
        const answer = document.getElementById('answer-' + id);
        const icon = document.getElementById('icon-' + id);
        if (answer.classList.contains('hidden')) {
            answer.classList.remove('hidden');
            icon.classList.add('rotate-180');
        } else {
            answer.classList.add('hidden');
            icon.classList.remove('rotate-180');
        }
    }

    function filterFAQ() {
        let input = document.getElementById('searchInput').value.toLowerCase();
        let items = document.getElementsByClassName('faq-item');
        for (let i = 0; i < items.length; i++) {
            let q = items[i].querySelector('.q-text').innerText.toLowerCase();
            let a = items[i].querySelector('.a-text').innerText.toLowerCase();
            let c = items[i].querySelector('.cat-label').innerText.toLowerCase();
            if (q.includes(input) || a.includes(input) || c.includes(input)) {
                items[i].style.display = "";
            } else {
                items[i].style.display = "none";
            }
        }
    }
</script>
@endsection