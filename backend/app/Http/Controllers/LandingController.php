<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\DonationCampaign;
use App\Models\Event;
use App\Models\FinancialSummary;
use App\Models\ProgramAllocation;
use App\Models\QurbanProgram;
use App\Models\SocialProgram;
use App\Models\ZakatRecord;
use Carbon\Carbon;

class LandingController extends Controller
{
    public function index()
    {
        $announcements = Announcement::where('status', 'published')
            ->orderByDesc('published_at')
            ->take(3)
            ->get();

        $events = Event::whereIn('status', ['planned', 'ongoing'])
            ->orderBy('event_date')
            ->take(3)
            ->get();

        $campaigns = DonationCampaign::where('status', 'active')
            ->orderByDesc('created_at')
            ->take(3)
            ->get();

        $latestSummary = FinancialSummary::where('is_published', true)
            ->orderByDesc('period_end')
            ->first();

        $totalIncome = 0;
        $totalExpense = 0;
        $closingBalance = 0;
        if ($latestSummary) {
            $totalIncome = $latestSummary->transactions()
                ->where('type', 'income')->where('status', 'verified')->sum('amount');
            $totalExpense = $latestSummary->transactions()
                ->where('type', 'expense')->where('status', 'verified')->sum('amount');
            $closingBalance = $latestSummary->opening_balance + $totalIncome - $totalExpense;
        }

        $transparency = ProgramAllocation::where('is_published', true)
            ->orderByDesc('published_at')
            ->take(3)
            ->get()
            ->map(function ($a) {
                $a->total_realized = $a->realizations()->sum('amount_used');
                return $a;
            });

        $zakatYear = Carbon::now()->year;
        $zakatTotal = ZakatRecord::where('period_year', $zakatYear)->sum('amount');

        $qurbanActive = QurbanProgram::where('status', 'open')->first();
        $socialActive = SocialProgram::where('status', 'active')->where('is_published', true)->first();

        $hasApk = file_exists(public_path('downloads/MasjidBGC.apk'));

        return view('welcome', compact(
            'announcements', 'events', 'campaigns',
            'latestSummary', 'totalIncome', 'totalExpense', 'closingBalance',
            'transparency', 'zakatTotal', 'zakatYear',
            'qurbanActive', 'socialActive', 'hasApk'
        ));
    }
}
