<?php

namespace App\Http\Controllers;

use App\Models\DataField;
use App\Models\DataModel;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DataModelController extends Controller
{
    /**
     * List semua "tabel virtual" yang sudah dibuat.
     */
    public function index()
    {
        $dataModels = DataModel::withCount(['fields', 'records'])
            ->latest()
            ->get();

        return Inertia::render('Admin/DataModels/Index', [
            'dataModels' => $dataModels,
        ]);
    }

    /**
     * Halaman form buat tabel virtual baru.
     */
    public function create()
    {
        return Inertia::render('Admin/DataModels/Create', [
            'fieldTypes' => DataField::TYPES,
        ]);
    }

    /**
     * Simpan tabel virtual baru beserta field-fieldnya.
     * Dikerjakan dalam 1 DB transaction biar aman (semua sukses atau semua batal).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'fields' => 'required|array|min:1',
            'fields.*.name' => 'required|string|max:255',
            'fields.*.type' => 'required|in:' . implode(',', DataField::TYPES),
            'fields.*.is_required' => 'boolean',
            'fields.*.options' => 'nullable|array',
        ]);

        $dataModel = DB::transaction(function () use ($validated) {
            $dataModel = DataModel::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);

            foreach ($validated['fields'] as $index => $fieldData) {
                $dataModel->fields()->create([
                    'name' => $fieldData['name'],
                    'type' => $fieldData['type'],
                    'is_required' => $fieldData['is_required'] ?? false,
                    'options' => $fieldData['options'] ?? null,
                    'order' => $index,
                ]);
            }

            // Otomatis daftarkan menu sidebar yang mengarah ke halaman data tabel ini.
            $maxOrder = Menu::whereNull('parent_id')->max('order');
            Menu::create([
                'label' => $dataModel->name,
                'icon' => 'Table2',
                'type' => 'page',
                'route' => route('admin.data-records.index', $dataModel->id, absolute: false),
                'data_model_id' => $dataModel->id,
                'order' => ($maxOrder ?? -1) + 1,
            ]);

            return $dataModel;
        });

        return redirect()
            ->route('admin.data-models.index')
            ->with('success', "Tabel \"{$dataModel->name}\" berhasil dibuat.");
    }

    /**
     * Hapus tabel virtual (otomatis hapus field & record-nya juga via cascade).
     * Menu sidebar yang terhubung ke tabel ini juga ikut dihapus.
     */
    public function destroy(DataModel $dataModel)
    {
        Menu::where('data_model_id', $dataModel->id)->delete();

        $dataModel->delete();

        return back()->with('success', 'Tabel berhasil dihapus.');
    }
}