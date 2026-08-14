<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class ExportController extends Controller
{
    // Method untuk menampilkan halaman export
    public function index()
    {
        return view('export.index'); // buat file resources/views/export/index.blade.php
    }

    // Method untuk proses export dan download database
   public function export()
{
    $databaseName = 'jadwal_pelayanan_gereja';

    // Ambil hanya TABLE, bukan VIEW
    $tables = DB::select("SHOW FULL TABLES FROM `$databaseName` WHERE Table_Type = 'BASE TABLE'");

    if (empty($tables)) {
        return back()->with('error', 'Database kosong atau tidak ditemukan.');
    }

    $tableKey = 'Tables_in_' . $databaseName;
    
    $sqlScript = "-- Backup database: $databaseName\n";
    $sqlScript .= "-- Export time: " . now()->toDateTimeString() . "\n\n";
    $sqlScript .= "SET FOREIGN_KEY_CHECKS=0;\n";

    foreach ($tables as $table) {
        $tableArray = (array) $table;
        $tableName = $tableArray[$tableKey];

        $createTable = DB::select("SHOW CREATE TABLE `$tableName`");
        if (!empty($createTable)) {
            $createSQL = $createTable[0]->{'Create Table'} ?? '';
            $sqlScript .= "\n-- ----------------------------\n";
            $sqlScript .= "-- Struktur tabel `$tableName`\n";
            $sqlScript .= "-- ----------------------------\n";
            $sqlScript .= "DROP TABLE IF EXISTS `$tableName`;\n";
            $sqlScript .= $createSQL . ";\n";
        }

        $rows = DB::table($tableName)->get();
        if ($rows->count() > 0) {
            $sqlScript .= "\n-- ----------------------------\n";
            $sqlScript .= "-- Data tabel `$tableName`\n";
            $sqlScript .= "-- ----------------------------\n";

            foreach ($rows as $row) {
                $columns = array_map(fn($col) => "`$col`", array_keys(get_object_vars($row)));
                $values = array_map(function ($val) {
                    if (is_null($val)) return "NULL";
                    return "'" . addslashes($val) . "'";
                }, array_values(get_object_vars($row)));

                $sqlScript .= "INSERT INTO `$tableName` (" . implode(", ", $columns) . ") VALUES (" . implode(", ", $values) . ");\n";
            }
        }
    }

    $sqlScript .= "\nSET FOREIGN_KEY_CHECKS=1;\n";

    $fileName = 'backup_' . $databaseName . '_' . date('Ymd_His') . '.mysql';
    $filePath = storage_path('app/' . $fileName);
    
    // Simpan file
    file_put_contents($filePath, $sqlScript);
    
    // Verifikasi file berhasil disimpan
    if (!file_exists($filePath)) {
        return back()->with('error', 'Gagal menyimpan file backup.');
    }

    return response()->download($filePath)->deleteFileAfterSend(true);
}
}
