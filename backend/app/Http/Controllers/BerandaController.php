<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\DonationCampaign;
use App\Models\Event;
use App\Models\FinancialSummary;
use App\Models\ProgramAllocation;
use Illuminate\Support\Facades\Auth;

class BerandaController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $announcements = Announcement::where('status', 'published')
            ->orderByDesc('published_at')
            ->take(5)
            ->get();

        $events = Event::whereIn('status', ['planned', 'ongoing'])
            ->orderBy('event_date')
            ->take(5)
            ->get();

        $campaigns = DonationCampaign::where('status', 'active')
            ->orderByDesc('created_at')
            ->take(4)
            ->get();

        $latestSummary = FinancialSummary::where('is_published', true)
            ->orderByDesc('period_end')
            ->first();

        $transparency = ProgramAllocation::where('is_published', true)
            ->orderByDesc('published_at')
            ->take(4)
            ->get()
            ->map(function ($a) {
                $a->total_realized = $a->realizations()->sum('amount_used');
                return $a;
            });

        $myDonations = $user->donations ?? collect();
        if (method_exists($user, 'donations')) {
            $myDonations = $user->donations()
                ->with('campaign')
                ->orderByDesc('created_at')
                ->take(5)
                ->get();
        }

        $mySocialApplications = \App\Models\SocialApplication::where('user_id', $user->id)
            ->with(['program'])
            ->orderByDesc('created_at')
            ->get();

        return view('beranda', compact(
            'user', 'announcements', 'events', 'campaigns',
            'latestSummary', 'transparency', 'myDonations', 'mySocialApplications'
        ));
    }
}
