<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MenuController extends Controller
{
    /**
     * Halaman admin: menu builder (drag-drop).
     */
    public function index()
    {
        // Ambil hanya parent (level 1), children di-load nested di dalamnya.
        $menus = Menu::whereNull('parent_id')
            ->orderBy('order')
            ->with('children')
            ->get();

        return Inertia::render('Admin/MenuBuilder', [
            'menus' => $menus,
        ]);
    }

    /**
     * Simpan menu baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'type' => 'required|in:link,page',
            'route' => 'nullable|string|max:255',
            'page_id' => 'nullable|exists:pages,id',
            'parent_id' => 'nullable|exists:menus,id',
        ]);

        // Validasi: cegah nested lebih dari 2 level.
        if (!empty($validated['parent_id'])) {
            $parent = Menu::findOrFail($validated['parent_id']);
            if ($parent->parent_id !== null) {
                return back()->withErrors([
                    'parent_id' => 'Menu hanya boleh maksimal 2 level (parent → child).',
                ]);
            }
        }

        $maxOrder = Menu::where('parent_id', $validated['parent_id'] ?? null)->max('order');
        $validated['order'] = ($maxOrder ?? -1) + 1;

        Menu::create($validated);

        return back()->with('success', 'Menu berhasil ditambahkan.');
    }

    /**
     * Update menu (label, icon, dll).
     */
    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'type' => 'required|in:link,page',
            'route' => 'nullable|string|max:255',
            'page_id' => 'nullable|exists:pages,id',
            'is_active' => 'boolean',
        ]);

        $menu->update($validated);

        return back()->with('success', 'Menu berhasil diperbarui.');
    }

    /**
     * Hapus menu.
     */
    public function destroy(Menu $menu)
    {
        $menu->delete();

        return back()->with('success', 'Menu berhasil dihapus.');
    }

    /**
     * Endpoint khusus untuk drag-drop reorder.
     * Menerima struktur menu lengkap (urutan + nesting) dari frontend,
     * lalu update semua order & parent_id sekaligus.
     */
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'menus' => 'required|array',
            'menus.*.id' => 'required|exists:menus,id',
            'menus.*.order' => 'required|integer',
            'menus.*.parent_id' => 'nullable|exists:menus,id',
        ]);

        foreach ($validated['menus'] as $item) {
            Menu::where('id', $item['id'])->update([
                'order' => $item['order'],
                'parent_id' => $item['parent_id'],
            ]);
        }

        return back()->with('success', 'Urutan menu berhasil disimpan.');
    }
}