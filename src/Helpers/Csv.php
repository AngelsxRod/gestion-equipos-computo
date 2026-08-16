<?php

declare(strict_types=1);

namespace App\Helpers;

final class Csv
{
    /**
     * @param array<int, array<int, string>> $filas
     * @param string[] $encabezados
     */
    public static function exportar(array $filas, array $encabezados, string $nombreArchivo): never
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');

        $salida = fopen('php://output', 'w');
        // BOM UTF-8 para que Excel muestre correctamente tildes y ñ
        fwrite($salida, "\xEF\xBB\xBF");
        fputcsv($salida, $encabezados);

        foreach ($filas as $fila) {
            fputcsv($salida, $fila);
        }

        fclose($salida);
        exit;
    }
}
