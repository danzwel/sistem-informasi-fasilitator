<?php

namespace App\Http\Controllers;

use App\Models\Fasilitator;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter', 'semua');

        $fasilitators = Fasilitator::with(['riwayatPendidikan', 'riwayatPelatihan'])->get();

        $checklist = $fasilitators->map(function ($f) {
            $adaPelatihan = $f->riwayatPelatihan->count() > 0;
            $sertifikatLengkap = $adaPelatihan
                ? $f->riwayatPelatihan->every(fn($r) => !empty($r->sertifikat))
                : false;

            return [
                'fasilitator' => $f,
                'foto' => !empty($f->foto),
                'ttd' => !empty($f->ttd),
                'pendidikan' => $f->riwayatPendidikan->count() > 0,
                'pelatihan' => $adaPelatihan,
                'sertifikat' => $sertifikatLengkap,
            ];
        });

        $belumLengkap = $checklist->filter(function ($item) {
            return !$item['foto'] || !$item['ttd'] || !$item['pendidikan'] || !$item['pelatihan'] || !$item['sertifikat'];
        });

        switch ($filter) {
            case 'foto':
                $ditampilkan = $checklist->filter(fn($item) => !$item['foto']);
                break;
            case 'ttd':
                $ditampilkan = $checklist->filter(fn($item) => !$item['ttd']);
                break;
            case 'pendidikan':
                $ditampilkan = $checklist->filter(fn($item) => !$item['pendidikan']);
                break;
            case 'pelatihan':
                $ditampilkan = $checklist->filter(fn($item) => !$item['pelatihan']);
                break;
            case 'sertifikat':
                $ditampilkan = $checklist->filter(fn($item) => !$item['sertifikat']);
                break;
            case 'belum_lengkap':
                $ditampilkan = $belumLengkap;
                break;
            default:
                $ditampilkan = $checklist;
        }

        return view('dashboard.index', [
            'ditampilkan' => $ditampilkan,
            'filter' => $filter,
            'totalFasilitator' => $fasilitators->count(),
            'totalAktif' => $fasilitators->where('status', 'aktif')->count(),
            'totalBelumLengkap' => $belumLengkap->count(),
        ]);
    }
}