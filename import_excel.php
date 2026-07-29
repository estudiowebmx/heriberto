<?php

require __DIR__.'/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$file = __DIR__.'/public/estructura-heriberto.xlsx';

if (!file_exists($file)) {
    echo "ERROR: File not found: {$file}\n";
    exit(1);
}

$spreadsheet = IOFactory::load($file);
$sheet = $spreadsheet->getActiveSheet();
$rows = $sheet->toArray();

// Headers are at row index 4 (5th row, 0-indexed)
$headers = $rows[4];
echo "Headers: " . json_encode($headers, JSON_UNESCAPED_UNICODE) . "\n";

$map = [
    'Nombre'    => 'nombre',
    'Ap Paterno'=> 'apellido_paterno',
    'Ap Materno'=> 'apellido_materno',
    'Celular'   => 'telefono',
    'Sección'    => 'seccion',
    'Promotor'  => 'promotor',
];

$cols = [];
foreach ($headers as $i => $h) {
    if (isset($map[$h])) {
        $cols[$i] = $map[$h];
    }
}
echo "Column mapping: " . json_encode($cols) . "\n";

$data = [];
for ($i = 5; $i < count($rows); $i++) {
    $row = $rows[$i];
    $record = [];
    foreach ($cols as $idx => $field) {
        $val = isset($row[$idx]) ? trim((string) $row[$idx]) : null;
        $record[$field] = $val !== '' && $val !== null ? $val : null;
    }

    if (empty($record['nombre']) || empty($record['apellido_paterno'])) {
        continue;
    }

    $record['telefono'] = $record['telefono'] ?? '0';
    $record['apellido_materno'] = $record['apellido_materno'] ?? '';
    $record['seccion'] = $record['seccion'] ?? '';
    $record['promotor'] = $record['promotor'] ?? '';

    $record['created_at'] = now();
    $record['updated_at'] = now();

    $data[] = $record;
}

echo "Records to insert: " . count($data) . "\n";

DB::table('personas')->truncate();
echo "Table truncated.\n";

$chunks = array_chunk($data, 500);
$inserted = 0;

DB::beginTransaction();
try {
    foreach ($chunks as $chunk) {
        DB::table('personas')->insert($chunk);
        $inserted += count($chunk);
        echo "Inserted {$inserted} records...\n";
    }
    DB::commit();
    echo "\nDONE! Total inserted: {$inserted} records.\n";
} catch (\Exception $e) {
    DB::rollBack();
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
