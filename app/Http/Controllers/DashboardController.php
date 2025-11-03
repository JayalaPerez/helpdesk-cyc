<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $statuses = config('helpdesk.statuses', ['Nuevo','En Progreso','Resuelto','Cerrado']);

        // Conteo SOLO del usuario logueado
        $mine = Ticket::select('status', DB::raw('COUNT(*) as c'))
            ->where('user_id', auth()->id())
            ->groupBy('status')
            ->pluck('c','status');

        // Conteo global (admin)
        $all = Ticket::select('status', DB::raw('COUNT(*) as c'))
            ->groupBy('status')
            ->pluck('c','status');

        // Últimos 10 tickets
        $lastTickets = Ticket::with(['requester','assignee'])
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard', compact('statuses','mine','all','lastTickets'));
    }
}

