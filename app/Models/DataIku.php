<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataIku extends Model
{
    use HasFactory;

    protected $table = 'data_iku';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        "perjanjian_kinerja_target_kumulatif",
        "perjanjian_kinerja_realisasi_kumulatif",
        "capaian_kinerja_kumulatif",
        "capaian_kinerja_target_setahun",
        "link_bukti_dukung_capaian",
        "upaya_yang_dilakukan",
        "link_bukti_dukung_upaya_yang_dilakukan",
        "kendala",
        "solusi_atas_kendala",
        "rencana_tidak_lanjut",
        "pic_tidak_lanjut",
        "tenggat_tidak_lanjut",
        "status",
        "upload_by",
        "approve_by",
        "reject_by",
        "triwulan_id",
        "sub_indikator_id",
        "bidang_id"
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'upload_by');
    }

    public function approved_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approve_by');
    }

    public function rejected_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reject_by');
    }

    public function sub_indikator(): BelongsTo
    {
        return $this->belongsTo(SubIndikator::class, 'sub_indikator_id');
    }

    public function indikator_penunjang(): BelongsTo
    {
        return $this->belongsTo(IndikatorPenunjang::class, 'indikator_penunjang_id');
    }

    public function indikator(): BelongsTo
    {
        return $this->belongsTo(Indikator::class, 'indikator_id');
    }

    public function bidang()
    {
        return $this->belongsTo(Bidang::class, 'bidang_id');
    }
}
