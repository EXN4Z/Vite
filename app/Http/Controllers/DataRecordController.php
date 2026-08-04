<?php

namespace App\Http\Controllers;

use App\Models\DataModel;
use App\Models\DataRecord;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DataRecordController extends Controller
{
    /**
     * Lihat semua data (record) dari 1 tabel virtual, dalam bentuk tabel.
     */
    public function index(DataModel $dataModel)
    {
        $dataModel->load('fields');

        $records = $dataModel->records()->latest()->get();

        return Inertia::render('Admin/DataModels/Records', [
            'dataModel' => $dataModel,
            'records' => $records,
        ]);
    }

    /**
     * Tambah data baru ke tabel virtual.
     * Validasi dibangun secara dinamis berdasarkan field yang terdaftar di data_fields.
     */
    public function store(Request $request, DataModel $dataModel)
    {
        $dataModel->load('fields');

        $rules = [];
        foreach ($dataModel->fields as $field) {
            $rule = $field->is_required ? 'required' : 'nullable';

            $rule .= match ($field->type) {
                'number' => '|numeric',
                'date' => '|date',
                'boolean' => '|boolean',
                default => '|string',
            };

            $rules["values.{$field->key}"] = $rule;
        }

        $validated = $request->validate($rules);

        $dataModel->records()->create([
            'values' => $validated['values'] ?? [],
            'created_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Data berhasil ditambahkan.');
    }

    /**
     * Hapus 1 baris data.
     */
    public function destroy(DataModel $dataModel, DataRecord $record)
    {
        $record->delete();

        return back()->with('success', 'Data berhasil dihapus.');
    }
}