<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Company extends Model
{
    use HasFactory;
    protected $guarded = [];

    public const TYPES = [
        'company',
        'school',
        'pesantren',
        'hospital',
        'government',
        'factory',
        'retail',
        'restaurant',
        'training',
        'organization',
        'transport',
        'remote',
        'sports'
    ];

    protected static function booted(): void
    {
        // Auto-generate subdomain dari nama tenant kalau belum diisi manual,
        // supaya proses "buat tenant baru" (dari superadmin) tidak wajib
        // mengisi subdomain sendiri.
        static::creating(function (Company $company) {
            if (empty($company->subdomain)) {
                $company->subdomain = static::generateUniqueSubdomain($company->name);
            } else {
                $company->subdomain = Str::slug($company->subdomain);
            }
        });
    }

    public static function generateUniqueSubdomain(string $name): string
    {
        $base = Str::slug($name) ?: 'tenant';
        $slug = $base;
        $i    = 1;

        while (static::where('subdomain', $slug)->exists()) {
            $slug = $base . '-' . (++$i);
        }

        return $slug;
    }

    /**
     * URL lengkap tenant ini, contoh: https://pt-maju.absen.milosgo.com
     */
    public function getSubdomainUrlAttribute(): ?string
    {
        $rootDomain = config('app.tenant_domain');

        if (!$this->subdomain || !$rootDomain) {
            return null;
        }

        $scheme = config('app.tenant_scheme', 'https');

        return "{$scheme}://{$this->subdomain}.{$rootDomain}";
    }

    public function scopeBySubdomain($query, string $subdomain)
    {
        return $query->where('subdomain', $subdomain);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function monthlyReports()
    {
        return $this->hasMany(MonthlyReport::class);
    }

        // ═══════════════════════════════════════════════════════════════════
    // RELASI — PESANTREN (MutabaahYaumiyah)
    // ═══════════════════════════════════════════════════════════════════

    /**
     * Semua record ngaji (mutabaah yaumiyah) di pesantren ini.
     *
     * Contoh:
     *   $pesantren->mutabaahYaumiyahs()->hariIni()->get();
     *   $pesantren->mutabaahYaumiyahs()->bulan(3, 2026)->count();
     *   $pesantren->mutabaahYaumiyahs()->ofSantri($santriId)->get();
     */
    public function mutabaahYaumiyahs()
    {
        return $this->hasMany(MutabaahYaumiyah::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(CompanySubscription::class, 'company_id');
    }

    public function activeSubscription()
    {
        return $this->hasOne(CompanySubscription::class, 'company_id')
            ->whereIn('status', ['trial', 'active'])
            ->latest('expires_at');
    }

    public function invoices()
    {
        return $this->hasMany(SubscriptionInvoice::class, 'company_id');
    }

    public function vaPayments()
    {
        return $this->hasMany(VaPayment::class, 'company_id');
    }

    // Cek cepat apakah fitur boleh diakses
    public function isSubscriptionActive(): bool
    {
        return $this->activeSubscription?->isActive() ?? false;
    }

    // Cek apakah company pernah trial
    public function hasUsedTrial(): bool
    {
        return $this->subscriptions()
            ->where('has_used_trial', true)
            ->exists();
    }

    public function billingRole(): string
    {
        return config("subscription.billing_roles.{$this->type}")
            ?? config('subscription.default_billing_role', 'hr');
    }

    public function classes()
    {
        return $this->hasMany(ClassRoom::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function attendanceDevices()
    {
        return $this->hasMany(AttendanceDevice::class);
    }

    public function studentAttendances()
    {
        return $this->hasMany(StudentAttendance::class);
    }

    // ═══════════════════════════════════════════════════════════════════
    // GAYA DASHBOARD (hijau = TPQ, biru = sekolah umum / pondok pesantren)
    // ═══════════════════════════════════════════════════════════════════

    public const DASHBOARD_SIMPLE = 'simple'; // hijau, TPQ
    public const DASHBOARD_FULL   = 'full';   // biru, grid modul

    /**
     * Urutan penentuan:
     *  1. kolom companies.dashboard_style kalau diisi superadmin
     *  2. school                      -> full  (biru)
     *  3. pesantren + is_boarding=1   -> full  (biru, pondok pesantren)
     *  4. pesantren + is_boarding=0   -> simple (hijau, TPQ)
     */
    public function dashboardStyle(): string
    {
        if (in_array($this->dashboard_style, [self::DASHBOARD_SIMPLE, self::DASHBOARD_FULL], true)) {
            return $this->dashboard_style;
        }

        if ($this->type === 'pesantren') {
            return $this->is_boarding ? self::DASHBOARD_FULL : self::DASHBOARD_SIMPLE;
        }

        return self::DASHBOARD_FULL;
    }

    /** Punya layanan asrama (pondok pesantren / sekolah pondok)? */
    public function hasBoarding(): bool
    {
        return (bool) $this->is_boarding
            || ($this->type === 'pesantren' && $this->dashboardStyle() === self::DASHBOARD_FULL);
    }
}
