<?php

namespace App\Http\Controllers;

use App\Models\DataIku;
use App\Models\Indikator;
use App\Models\IndikatorPenunjang;
use App\Models\SubIndikator;
use App\Models\Triwulan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminApprovalController extends Controller
{
    public function view_pending_data(Request $request)
    {
        $search = $request->input('search');
        $bidangId = Auth::user()->bidang_id;

        $triwulan_id = $request->input('triwulan');

        // Inisialisasi query
        $dataIkuQuery = DataIku::where('status', 'pending')
            ->where(function ($query) use ($bidangId) {
                $query->whereHas('sub_indikator', function ($q) use ($bidangId) {
                    $q->whereNull('bidang_id')
                        ->orWhere('bidang_id', $bidangId);
                })
                    ->orWhereHas('indikator', function ($q) use ($bidangId) {
                        $q->whereNull('bidang_id')
                            ->orWhere('bidang_id', $bidangId);
                    })
                    ->orWhereHas('indikator_penunjang');
            })
            ->with(['sub_indikator', 'indikator_penunjang', 'indikator', 'user'])
            ->orderBy('created_at', 'desc');

        // Tambahkan kondisi pencarian jika ada
        if ($search) {
            $dataIkuQuery->where(function ($query) use ($search) {
                $query->whereHas('sub_indikator', function ($q) use ($search) {
                    $q->where('sub_indikator', 'like', '%' . $search . '%');
                })
                    ->orWhereHas('indikator_penunjang', function ($q) use ($search) {
                        $q->where('indikator_penunjang', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('indikator', function ($q) use ($search) {
                        $q->where('indikator', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        // Paginate the results
        $dataIku = $dataIkuQuery->paginate(5);

        // Return view with the data
        return view('adminapproval.dashboard', [
            'dataIku' => $dataIku,
            'triwulan' => $triwulan_id
        ]);
    }

    public function view_approved_master_data(Request $request)
    {
        $search = $request->input('search');
        $bidangId = Auth::user()->bidang_id;

        // Inisialisasi query
        $dataIkuQuery = DataIku::where('status', 'approved_by_ab')
            ->whereHas('sub_indikator', function ($query) use ($bidangId) {
                $query->whereNull('bidang_id')
                    ->orWhere('bidang_id', $bidangId);
            })
            ->with(['sub_indikator', 'indikator_penunjang', 'indikator', 'user', 'approved_by']);

        // Tambahkan kondisi pencarian jika ada
        if ($search) {
            $dataIkuQuery->where(function ($query) use ($search) {
                $query->whereHas('sub_indikator', function ($q) use ($search) {
                    $q->where('sub_indikator', 'like', '%' . $search . '%');
                })
                    ->orWhereHas('indikator_penunjang', function ($q) use ($search) {
                        $q->where('indikator_penunjang', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('indikator', function ($q) use ($search) {
                        $q->where('indikator', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        // Paginate the results
        $dataIku = $dataIkuQuery->paginate(5);

        // Return view with the data
        return view('adminapproval.approved-master-data', [
            'dataIku' => $dataIku,
        ]);
    }

    public function view_rejected_master_data(Request $request)
    {
        $search = $request->input('search');
        $bidangId = Auth::user()->bidang_id;

        // Inisialisasi query
        $dataIkuQuery = DataIku::where('status', 'rejected')
        ->where(function ($query) use ($bidangId) {
            $query->whereHas('sub_indikator', function ($q) use ($bidangId) {
                $q->whereNull('bidang_id')
                    ->orWhere('bidang_id', $bidangId);
            })
                ->orWhereHas('indikator', function ($q) use ($bidangId) {
                    $q->whereNull('bidang_id')
                        ->orWhere('bidang_id', $bidangId);
                })
                ->orWhereHas('indikator_penunjang');
        })
        ->with(['sub_indikator', 'indikator_penunjang', 'indikator', 'user', 'rejected_by'])
        ->orderBy('created_at', 'desc');

        // Tambahkan kondisi pencarian jika ada
        if ($search) {
            $dataIkuQuery->where(function ($query) use ($search) {
                $query->whereHas('sub_indikator', function ($q) use ($search) {
                    $q->where('sub_indikator', 'like', '%' . $search . '%');
                })
                    ->orWhereHas('indikator_penunjang', function ($q) use ($search) {
                        $q->where('indikator_penunjang', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('indikator', function ($q) use ($search) {
                        $q->where('indikator', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        // Paginate the results
        $dataIku = $dataIkuQuery->paginate(5);

        // Return view with the data
        return view('adminapproval.rejected-master-data', [
            'dataIku' => $dataIku,
        ]);
    }

    public function view_aksi_master_data(Request $request, $type, $id)
    {
        $triwulan_id = $request->input('triwulan');

        // Fetch entity based on type and id
        if ($type === 'sub_indikator') {
            $entity = SubIndikator::find($id);
            $entityName = $entity->sub_indikator ?? null;
        } elseif ($type === 'indikator_penunjang') {
            $entity = IndikatorPenunjang::find($id);
            $entityName = $entity->indikator_penunjang ?? null;
        } elseif ($type === 'indikator') {
            $entity = Indikator::find($id);
            $entityName = $entity->indikator ?? null;
        } else {
            $entity = null;
            $entityName = null;
        }

        if (!$entity) {
            return redirect()->back()->with('error', 'Entitas tidak ditemukan');
        }

        // Fetch DataIku based on entity id
        $dataIku = DataIku::where(function ($query) use ($id, $type) {
            if ($type === 'sub_indikator') {
                $query->where('sub_indikator_id', $id);
            } elseif ($type === 'indikator_penunjang') {
                $query->where('indikator_penunjang_id', $id);
            } elseif ($type === 'indikator') {
                $query->where('indikator_id', $id);
            }
        })
            ->where('triwulan_id', $triwulan_id)
            ->first();

        if (!$dataIku) {
            return redirect()->back()->with('error', 'Data IKU tidak ditemukan');
        }

        $selectedTriwulan = $request->query('triwulan', null);
        $triwulanStatus = Triwulan::find($selectedTriwulan)->status ?? null;

        // Memeriksa apakah triwulan memiliki status 'close'
        if ($triwulanStatus === 'close') {
            // Jika triwulan memiliki status 'close', lakukan redirect atau tampilkan pesan kesalahan
            return redirect()->back()->with([
                'error' => [
                    "title" => "Cannot Edit Data",
                    "message" => "Triwulan sedang ditutup"
                ]
            ]);
        }

        return view('adminapproval.aksi-master-data', [
            'entityType' => $type,
            'entityName' => $entityName,
            'entityId' => $id,
            'dataIku' => $dataIku,
            'triwulan' => $triwulan_id,
            'triwulanStatus' => $triwulanStatus
        ]);
    }

    public function approve_data(Request $request, $id)
    {
        $selectedTriwulan = (int) $request->query('triwulan', null);

        // Cari dataIku berdasarkan id dan triwulan_id
        // $dataIku = DataIku::where('triwulan_id', $selectedTriwulan)
        //     ->firstOrFail();
        $dataIku = DataIku::find($id);

        // Jika data ditemukan, ubah statusnya
        $dataIku->status = 'approved_by_ap';
        $dataIku->approve_by = Auth::id();
        $dataIku->save();

        return response()->json([
            'success' => [
                "title" => "Data Approve Succesfully",
                "message" => "Data berhasil disetujui"
            ]
        ], 200);
    }

    public function reject_data(Request $request, $id)
    {
        $selectedTriwulan = (int) $request->query('triwulan', null);

        // Cari dataIku berdasarkan id dan triwulan_id
        // $dataIku = DataIku::where('triwulan_id', $selectedTriwulan)
        //     ->firstOrFail();
        $dataIku = DataIku::find($id);

        $dataIku->status = 'rejected';
        $dataIku->reject_comment = $request->reject_comment;
        $dataIku->reject_by = Auth::id();
        $dataIku->save();

        return response()->json([
            'success' => [
                "title" => "Data Reject Succesfully",
                "message" => "Data berhasil ditolak"
            ]
        ], 200);
    }

    public function update_master_data(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'perjanjian_kinerja_target_kumulatif' => 'required|integer',
            'perjanjian_kinerja_realisasi_kumulatif' => 'required|integer',
            'capaian_kinerja_kumulatif' => 'required|numeric',
            'capaian_kinerja_target_setahun' => 'required|numeric',
            'link_bukti_dukung_capaian' => 'required|string',
            'upaya_yang_dilakukan' => 'required|string',
            'link_bukti_dukung_upaya_yang_dilakukan' => 'required|string',
            'kendala' => 'required|string',
            'solusi_atas_kendala' => 'required|string',
            'rencana_tidak_lanjut' => 'required|string',
            'pic_tidak_lanjut' => 'required|string',
            'tenggat_tidak_lanjut' => 'required|date',
        ]);

        // Cari data yang akan diupdate
        $data = DataIku::findOrFail($id);

        // Memeriksa apakah semua input sama dengan nilai sebelumnya
        if (
            $data->perjanjian_kinerja_target_kumulatif == $request->input('perjanjian_kinerja_target_kumulatif') &&
            $data->perjanjian_kinerja_realisasi_kumulatif == $request->input('perjanjian_kinerja_realisasi_kumulatif') &&
            $data->capaian_kinerja_kumulatif == $request->input('capaian_kinerja_kumulatif') &&
            $data->capaian_kinerja_target_setahun == $request->input('capaian_kinerja_target_setahun') &&
            $data->link_bukti_dukung_capaian == $request->input('link_bukti_dukung_capaian') &&
            $data->upaya_yang_dilakukan == $request->input('upaya_yang_dilakukan') &&
            $data->link_bukti_dukung_upaya_yang_dilakukan == $request->input('link_bukti_dukung_upaya_yang_dilakukan') &&
            $data->kendala == $request->input('kendala') &&
            $data->solusi_atas_kendala == $request->input('solusi_atas_kendala') &&
            $data->rencana_tidak_lanjut == $request->input('rencana_tidak_lanjut') &&
            $data->pic_tidak_lanjut == $request->input('pic_tidak_lanjut') &&
            $data->tenggat_tidak_lanjut == $request->input('tenggat_tidak_lanjut')
        ) {
            return redirect()->back()->with([
                'warning' => [
                    "title" => "No Changes Made",
                    "message" => "Tidak ada perubahan yang dilakukan."
                ]
            ]);
        }

        // Memperbarui data
        $data->update([
            'perjanjian_kinerja_target_kumulatif' => $request->input('perjanjian_kinerja_target_kumulatif'),
            'perjanjian_kinerja_realisasi_kumulatif' => $request->input('perjanjian_kinerja_realisasi_kumulatif'),
            'capaian_kinerja_kumulatif' => $request->input('capaian_kinerja_kumulatif'),
            'capaian_kinerja_target_setahun' => $request->input('capaian_kinerja_target_setahun'),
            'link_bukti_dukung_capaian' => $request->input('link_bukti_dukung_capaian'),
            'upaya_yang_dilakukan' => $request->input('upaya_yang_dilakukan'),
            'link_bukti_dukung_upaya_yang_dilakukan' => $request->input('link_bukti_dukung_upaya_yang_dilakukan'),
            'kendala' => $request->input('kendala'),
            'solusi_atas_kendala' => $request->input('solusi_atas_kendala'),
            'rencana_tidak_lanjut' => $request->input('rencana_tidak_lanjut'),
            'pic_tidak_lanjut' => $request->input('pic_tidak_lanjut'),
            'tenggat_tidak_lanjut' => $request->input('tenggat_tidak_lanjut'),
            'upload_by' => Auth::id()
        ]);

        try {
            // Memasukkan data ke dalam database
            $data->save();

            return redirect()->back()->with([
                'success' => [
                    "title" => "Data Updated Successfully",
                    "message" => "Data berhasil diperbarui."
                ]
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                return redirect()->back()->withErrors(['error' => 'Duplikat entri terdeteksi. Data dengan ID yang sama sudah ada.'])->withInput();
            }
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan data.'])->withInput();
        }
    }

}
