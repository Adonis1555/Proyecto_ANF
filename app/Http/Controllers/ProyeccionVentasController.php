<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProyeccionVentasController extends Controller
{
    /**
     * Mostrar la vista principal
     */
    public function index()
    {
        if (!session()->has('usuario_id')) {
            return redirect()->route('login');
        }
        return view('proyeccion-ventas.index');
    }

    /**
     * Procesar datos de ventas y generar proyecciones
     */
    public function proyectar(Request $request): JsonResponse
    {
        try {
            $datos = [];

            // Si vienen datos del archivo Excel
            if ($request->hasFile('archivo')) {
                $datos = $this->procesarExcel($request->file('archivo'));
            } 
            // Si vienen datos digitados manualmente
            elseif ($request->has('datos_manuales')) {
                $datos = $this->procesarDatosManuales($request->datos_manuales);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Debe proporcionar datos mediante archivo Excel o ingreso manual'
                ], 400);
            }

            // Validar que tengamos al menos datos
            if (empty($datos)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron datos válidos para procesar'
                ], 400);
            }

            // Si solo hay 11 meses, completar el mes 12 con el promedio
            if (count($datos) == 11) {
                $promedio = array_sum($datos) / count($datos);
                $datos[12] = round($promedio, 2);
            }

            // Validar que tengamos exactamente 12 meses
            if (count($datos) != 12) {
                return response()->json([
                    'success' => false,
                    'message' => 'Se requieren exactamente 12 meses de datos. Se encontraron: ' . count($datos)
                ], 400);
            }

            // Calcular proyecciones usando los tres métodos
            $proyecciones = [
                'minimos_cuadrados' => $this->calcularMinimosCuadrados($datos),
                'incremento_porcentual' => $this->calcularIncrementoPortcentual($datos),
                'incremento_absoluto' => $this->calcularIncrementoAbsoluto($datos)
            ];

            // Calcular estadísticas adicionales
            $estadisticas = $this->calcularEstadisticas($datos);

            return response()->json([
                'success' => true,
                'data' => [
                    'datos_historicos' => $datos,
                    'proyecciones' => $proyecciones,
                    'estadisticas' => $estadisticas
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar los datos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Procesar archivo Excel
     */
    private function procesarExcel($archivo): array
    {
        try {
            $spreadsheet = IOFactory::load($archivo->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $datos = [];
            
            // Aca
            // Leer datos desde la fila 2 (asumiendo que la fila 1 es encabezado)
            // Columna A: Mes, Columna B: Ventas
            for ($row = 2; $row <= 13; $row++) {
                $valor = $worksheet->getCell('B' . $row)->getValue();
                if ($valor !== null && is_numeric($valor)) {
                    $datos[$row - 1] = floatval($valor);
                }
            }

            return $datos;
        } catch (\Exception $e) {
            throw new \Exception('Error al leer el archivo Excel: ' . $e->getMessage());
        }
    }

    /**
     * Procesar datos ingresados manualmente
     */
    private function procesarDatosManuales($datosManuales): array
    {
        $datos = [];
        
        if (is_string($datosManuales)) {
            $datosManuales = json_decode($datosManuales, true);
        }

        if (!is_array($datosManuales)) {
            throw new \Exception('Formato de datos inválido');
        }

        foreach ($datosManuales as $mes => $valor) {
            if ($valor !== null && $valor !== '' && is_numeric($valor)) {
                $datos[$mes] = floatval($valor);
            }
        }

        ksort($datos);
        return $datos;
    }

    /**
     * Minimos Cuadrados
     * Y = a + bX donde: b = (nΣxy - ΣxΣy) / (nΣx² - (Σx)²), a = ȳ - bx̄
     */
    private function calcularMinimosCuadrados(array $datos): array
    {
        $n = count($datos);
        $x = range(1, $n); // Meses: 1, 2, 3, ..., 12
        $y = array_values($datos); // Ventas

        $sumX = array_sum($x);
        $sumY = array_sum($y);
        $sumXY = 0;
        $sumX2 = 0;

        for ($i = 0; $i < $n; $i++) {
            $sumXY += $x[$i] * $y[$i];
            $sumX2 += $x[$i] * $x[$i];
        }

        // Calcular pendiente (b) e intercepto (a)
        $b = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
        $a = ($sumY - $b * $sumX) / $n;

        // Generar proyecciones para los próximos 12 meses
        $proyecciones = [];
        for ($i = 1; $i <= 12; $i++) {
            $mes = $n + $i;
            $proyecciones[$i] = round($a + $b * $mes, 2);
        }

        return [
            'metodo' => 'Mínimos Cuadrados',
            'formula' => "Y = {$a} + {$b}X",
            'coeficientes' => [
                'a' => round($a, 4),
                'b' => round($b, 4)
            ],
            'proyecciones' => $proyecciones
        ];
    }

    /**
     * Incremento Porcentual
     * Calcula el promedio de incrementos porcentuales mes a mes
     */
    private function calcularIncrementoPortcentual(array $datos): array
    {
        $incrementos = [];
        $valores = array_values($datos);

        // Calcular incrementos porcentuales entre meses consecutivos
        for ($i = 1; $i < count($valores); $i++) {
            if ($valores[$i - 1] != 0) {
                $incremento = (($valores[$i] - $valores[$i - 1]) / $valores[$i - 1]) * 100;
                $incrementos[] = $incremento;
            }
        }

        // Promedio de incrementos porcentuales
        $promedioIncremento = !empty($incrementos) ? array_sum($incrementos) / count($incrementos) : 0;

        // Generar proyecciones
        $proyecciones = [];
        $ultimoValor = end($valores);

        for ($i = 1; $i <= 12; $i++) {
            $ultimoValor = $ultimoValor * (1 + $promedioIncremento / 100);
            $proyecciones[$i] = round($ultimoValor, 2);
        }

        return [
            'metodo' => 'Incremento Porcentual',
            'incremento_promedio' => round($promedioIncremento, 2) . '%',
            'proyecciones' => $proyecciones
        ];
    }

    /**
     * Incremento Absoluto
     * Calcula el promedio de incrementos absolutos mes a mes
     */
    private function calcularIncrementoAbsoluto(array $datos): array
    {
        $incrementos = [];
        $valores = array_values($datos);

        // Calcular incrementos absolutos entre meses consecutivos
        for ($i = 1; $i < count($valores); $i++) {
            $incremento = $valores[$i] - $valores[$i - 1];
            $incrementos[] = $incremento;
        }

        // Promedio de incrementos absolutos
        $promedioIncremento = !empty($incrementos) ? array_sum($incrementos) / count($incrementos) : 0;

        // Generar proyecciones
        $proyecciones = [];
        $ultimoValor = end($valores);

        for ($i = 1; $i <= 12; $i++) {
            $ultimoValor = $ultimoValor + $promedioIncremento;
            $proyecciones[$i] = round($ultimoValor, 2);
        }

        return [
            'metodo' => 'Incremento Absoluto',
            'incremento_promedio' => round($promedioIncremento, 2),
            'proyecciones' => $proyecciones
        ];
    }

    /**
     * Calcular estadísticas descriptivas de los datos históricos
     */
    private function calcularEstadisticas(array $datos): array
    {
        $valores = array_values($datos);
        $n = count($valores);

        $promedio = array_sum($valores) / $n;
        $maximo = max($valores);
        $minimo = min($valores);

        // Calcular desviación estándar
        $sumatoria = 0;
        foreach ($valores as $valor) {
            $sumatoria += pow($valor - $promedio, 2);
        }
        $desviacion = sqrt($sumatoria / $n);

        // Calcular tendencia general
        $tendencia = ($valores[$n - 1] - $valores[0]) / $valores[0] * 100;

        return [
            'promedio' => round($promedio, 2),
            'maximo' => round($maximo, 2),
            'minimo' => round($minimo, 2),
            'desviacion_estandar' => round($desviacion, 2),
            'tendencia_general' => round($tendencia, 2) . '%'
        ];
    }

    /**
     * Descargar plantilla Excel
     */
    public function descargarPlantilla()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezados
        $sheet->setCellValue('A1', 'Mes');
        $sheet->setCellValue('B1', 'Ventas');

        // Estilo de encabezados
        $sheet->getStyle('A1:B1')->getFont()->setBold(true);
        $sheet->getStyle('A1:B1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('0D6EFD');
        $sheet->getStyle('A1:B1')->getFont()->getColor()->setRGB('FFFFFF');

        // Meses
        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 
                  'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        
        for ($i = 0; $i < 12; $i++) {
            $row = $i + 2;
            $sheet->setCellValue('A' . $row, $meses[$i]);
            $sheet->setCellValue('B' . $row, ''); // Celda vacía para ingresar datos
        }

        // Ajustar ancho de columnas
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(15);

        // Guardar archivo
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        $fileName = 'plantilla_ventas.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        
        $writer->save($temp_file);

        return response()->download($temp_file, $fileName)->deleteFileAfterSend(true);
    }
}