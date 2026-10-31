<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function summary(Request $request): JsonResponse
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'type' => 'nullable|in:all,income,expense',
        ]);

        $data = $this->reportService->getReportData(
            $request->date_from,
            $request->date_to,
            $request->get('type', 'all')
        );

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function exportPdf(Request $request)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'type' => 'nullable|in:all,income,expense',
        ]);

        $user = $request->user();
        $data = $this->reportService->getReportData(
            $request->date_from,
            $request->date_to,
            $request->get('type', 'all')
        );

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'export_pdf',
            'description' => 'Mengekspor laporan keuangan PDF periode ' . $request->date_from . ' s/d ' . $request->date_to,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $pdf = $this->reportService->generatePdf($data, $user);
        $fileName = 'Laporan_Keuangan_Masjid_BGC_' . $request->date_from . '_sd_' . $request->date_to . '.pdf';

        return $pdf->download($fileName);
    }

    public function exportExcel(Request $request): BinaryFileResponse
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'type' => 'nullable|in:all,income,expense',
        ]);

        $user = $request->user();
        $data = $this->reportService->getReportData(
            $request->date_from,
            $request->date_to,
            $request->get('type', 'all')
        );

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'export_excel',
            'description' => 'Mengekspor laporan keuangan Excel periode ' . $request->date_from . ' s/d ' . $request->date_to,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $fileName = 'Laporan_Keuangan_Masjid_BGC_' . $request->date_from . '_sd_' . $request->date_to . '.xlsx';
        $tempPath = storage_path('app/' . $fileName);

        $this->reportService->generateExcel($data, $user, $tempPath);

        return response()->download($tempPath, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}
