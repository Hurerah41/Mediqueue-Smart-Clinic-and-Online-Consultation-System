<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Clinic;
use App\Models\Review;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('welcome', [
            'areas' => Area::withCount('clinics')->orderBy('name')->get(),
            'clinics' => Clinic::query()
                ->with([
                    'area',
                    'doctors.user',
                    'doctors.specialization',
                    'doctors.reviews' => fn ($query) => $query->where('review_type', Review::TYPE_DOCTOR),
                ])
                ->where('is_active', true)
                ->latest()
                ->take(6)
                ->get(),
        ]);
    }
}
