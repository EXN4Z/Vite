<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
     * Kalau ada "blocks" (dari page builder), otomatis buat Page + PageBlock
     * dan sambungkan menu ini ke halaman itu (type = page).
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
            'blocks' => 'nullable|array',
            'blocks.*.type' => 'required_with:blocks|in:text,table,chart,card',
            'blocks.*.config' => 'nullable|array',
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

        DB::transaction(function () use ($validated, $maxOrder) {
            $pageId = $validated['page_id'] ?? null;
            $route = $validated['route'] ?? null;

            // Kalau ada blocks dikirim, buat Page baru otomatis + simpan blok-bloknya,
            // dan arahkan route menu ini ke halaman generic yang merender Page tersebut.
            if (!empty($validated['blocks'])) {
                $page = Page::create(['name' => $validated['label']]);

                foreach ($validated['blocks'] as $index => $blockData) {
                    $page->blocks()->create([
                        'type' => $blockData['type'],
                        'config' => $blockData['config'] ?? [],
                        'order' => $index,
                    ]);
                }

                $pageId = $page->id;
                $route = route('admin.pages.show', $page->id, absolute: false);
            }

            Menu::create([
                'label' => $validated['label'],
                'icon' => $validated['icon'] ?? null,
                'type' => $validated['type'],
                'route' => $route,
                'page_id' => $pageId,
                'parent_id' => $validated['parent_id'] ?? null,
                'order' => ($maxOrder ?? -1) + 1,
            ]);
        });

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