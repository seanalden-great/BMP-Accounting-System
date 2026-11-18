<?php

// namespace App\Http\Controllers;

// use App\Models\Faq;
// use Illuminate\Http\Request;

// class FaqController extends Controller
// {
//     // Menampilkan halaman daftar FAQ (GET /faqs)
//     public function index()
//     {
//         $faqs = Faq::orderBy('category', 'asc')
//                    ->orderBy('id', 'desc')
//                    ->get();

//         return view('faqs.index', compact('faqs'));
//     }

//     // Menyimpan FAQ baru (POST /faqs)
//     public function store(Request $request)
//     {
//         $validated = $request->validate([
//             'category' => 'required|string|max:50',
//             'question' => 'required|string',
//             'answer'   => 'required|string',
//         ]);

//         try {
//             Faq::create($validated);
//             return redirect()->route('faqs.index')
//                 ->with('success', 'FAQ berhasil ditambahkan!');
//         } catch (\Exception $e) {
//             return redirect()->back()
//                 ->withInput()
//                 ->with('error', 'Gagal menyimpan FAQ.');
//         }
//     }

//     // Memperbarui data FAQ (PUT /faqs/{id}) --> (Tambahan ekstra untuk kemudahan Anda)
//     public function update(Request $request, $id)
//     {
//         $faq = Faq::findOrFail($id);

//         $validated = $request->validate([
//             'category' => 'required|string|max:50',
//             'question' => 'required|string',
//             'answer'   => 'required|string',
//         ]);

//         try {
//             $faq->update($validated);
//             return redirect()->route('faqs.index')
//                 ->with('success', 'FAQ berhasil diperbarui!');
//         } catch (\Exception $e) {
//             return redirect()->back()
//                 ->withInput()
//                 ->with('error', 'Gagal mengupdate FAQ.');
//         }
//     }

//     // Menghapus FAQ (DELETE /faqs/{id})
//     public function destroy($id)
//     {
//         try {
//             $faq = Faq::findOrFail($id);
//             $faq->delete();

//             return redirect()->route('faqs.index')
//                 ->with('success', 'FAQ berhasil dihapus!');
//         } catch (\Exception $e) {
//             return redirect()->back()
//                 ->with('error', 'Gagal menghapus FAQ.');
//         }
//     }
// }

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;
use Stichoza\GoogleTranslate\GoogleTranslate; // Panggil library penerjemah

class FaqController extends Controller
{
    // Menampilkan halaman daftar FAQ (GET /faqs)
    public function index()
    {
        $faqs = Faq::orderBy('category', 'asc')
                   ->orderBy('id', 'desc')
                   ->get();

        // 1. Cek Bahasa Saat Ini
        $locale = app()->getLocale();

        // 2. Jika bukan 'id', Lakukan Auto-Translate pada Data Database
        if ($locale !== 'id') {
            // Inisialisasi penerjemah (Dari ID ke EN)
            $tr = new GoogleTranslate();
            $tr->setSource('id');
            $tr->setTarget($locale);

            // Translasi (on-the-fly) pada setiap baris data
            $faqs->transform(function ($faq) use ($tr) {
                try {
                    $faq->category = $tr->translate($faq->category);
                    $faq->question = $tr->translate($faq->question);
                    $faq->answer   = $tr->translate($faq->answer);
                } catch (\Exception $e) {
                    // Jika Google API gagal, biarkan dalam bahasa asli
                }
                return $faq;
            });
        }

        return view('faqs.index', compact('faqs'));
    }

    // Menyimpan FAQ baru (POST /faqs)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:50',
            'question' => 'required|string',
            'answer'   => 'required|string',
        ]);

        try {
            Faq::create($validated);
            return redirect()->route('faqs.index')
                ->with('success', 'FAQ berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan FAQ.');
        }
    }

    // Memperbarui data FAQ (PUT /faqs/{id})
    public function update(Request $request, $id)
    {
        $faq = Faq::findOrFail($id);

        $validated = $request->validate([
            'category' => 'required|string|max:50',
            'question' => 'required|string',
            'answer'   => 'required|string',
        ]);

        try {
            $faq->update($validated);
            return redirect()->route('faqs.index')
                ->with('success', 'FAQ berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate FAQ.');
        }
    }

    // Menghapus FAQ (DELETE /faqs/{id})
    public function destroy($id)
    {
        try {
            $faq = Faq::findOrFail($id);
            $faq->delete();

            return redirect()->route('faqs.index')
                ->with('success', 'FAQ berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus FAQ.');
        }
    }
}
