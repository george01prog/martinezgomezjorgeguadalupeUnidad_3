<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    public function index(Request $request, ReportService $reports): View
    {
        $filters = $this->filters($request);
        $report = $filters['report'];

        $query = $reports->query($report, $filters);
        $summary = $reports->summary($report, $query);
        $results = $query->paginate(15)->withQueryString();

        return view('reports.index', [
            'report' => $report,
            'reportList' => ReportService::REPORTS,
            'title' => ReportService::TITLES[$report],
            'isMovement' => $reports->isMovementReport($report),
            'filters' => $filters,
            'summary' => $summary,
            'results' => $results,
            'products' => Product::orderBy('name')->get(['id', 'sku', 'name']),
            'categories' => Category::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function export(Request $request, string $format, ReportService $reports): Response
    {
        $filters = $this->filters($request);
        $report = $filters['report'];

        $query = $reports->query($report, $filters);
        $headers = $reports->headers($report);
        $models = $query->get();
        $filename = 'reporte-' . str_replace('_', '-', $report) . '-' . now()->format('Ymd-His');

        if ($format === 'csv') {
            return response()->streamDownload(function () use ($models, $headers, $reports, $report) {
                $out = fopen('php://output', 'w');
                fwrite($out, "\xEF\xBB\xBF"); // BOM UTF-8 para que Excel lea bien los acentos
                fputcsv($out, $headers, ',', '"', '\\');

                foreach ($models as $model) {
                    fputcsv($out, $reports->row($report, $model), ',', '"', '\\');
                }

                fclose($out);
            }, $filename . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
        }

        $rows = $models->map(fn ($model) => $reports->row($report, $model))->all();

        $pdf = Pdf::loadView('reports.pdf', [
            'title' => ReportService::TITLES[$report],
            'headers' => $headers,
            'rows' => $rows,
            'summary' => $reports->summary($report, $query),
            'filterLines' => $this->describeFilters($filters, $report),
            'generatedBy' => $request->user()->name,
            'generatedAt' => now()->format('d/m/Y H:i'),
        ])->setPaper('a4', 'landscape');

        return $pdf->download($filename . '.pdf');
    }

    /** Valida y normaliza los filtros recibidos por query string. */
    private function filters(Request $request): array
    {
        $data = $request->validate([
            'report' => ['nullable', Rule::in(array_keys(ReportService::REPORTS))],
            'product' => ['nullable', 'integer'],
            'category' => ['nullable', 'integer'],
            'type' => ['nullable', Rule::in(array_keys(InventoryMovement::TYPES))],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ], [
            'report.in' => 'El tipo de reporte no es válido.',
            'type.in' => 'El tipo de movimiento no es válido.',
            'from.date' => 'La fecha inicial no es válida.',
            'to.date' => 'La fecha final no es válida.',
            'to.after_or_equal' => 'La fecha final no puede ser anterior a la fecha inicial.',
        ]);

        return [
            'report' => $data['report'] ?? 'products',
            'product' => $data['product'] ?? null,
            'category' => $data['category'] ?? null,
            'type' => $data['type'] ?? null,
            'from' => $data['from'] ?? null,
            'to' => $data['to'] ?? null,
        ];
    }

    /** Texto legible de los filtros aplicados (para el PDF). */
    private function describeFilters(array $f, string $report): array
    {
        $lines = [];

        if ($f['category']) {
            $lines[] = 'Categoría: ' . (Category::find($f['category'])?->name ?? '—');
        }

        if ($f['product']) {
            $p = Product::find($f['product']);
            $lines[] = 'Producto: ' . ($p ? "{$p->sku} · {$p->name}" : '—');
        }

        if ($f['type'] && $report === 'movements') {
            $lines[] = 'Tipo: ' . (InventoryMovement::TYPES[$f['type']] ?? $f['type']);
        }

        if ($f['from']) {
            $lines[] = 'Desde: ' . Carbon::parse($f['from'])->format('d/m/Y');
        }

        if ($f['to']) {
            $lines[] = 'Hasta: ' . Carbon::parse($f['to'])->format('d/m/Y');
        }

        return $lines;
    }
}