<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Clinic;
use App\Models\Review;
use App\Models\Specialization;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $view = config('public_ui.v2') ? 'public.welcome' : 'welcome';

        return view($view, [
            'areas' => Area::withCount('clinics')->orderBy('name')->get(),
            'specializations' => Specialization::orderBy('name')->get(),
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
