<?php
namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToTenant
{
protected static function bootBelongsToTenant()
{
// Setiap query model ini otomatis ditambahkan WHERE tenant_id = tenant yang sedang aktif
static::addGlobalScope('tenant', function (Builder $builder) {
if (app()->bound('currentTenant')) {
$builder->where('tenant_id', app('currentTenant')->id);
}
});

// Setiap kali bikin data baru, tenant_id otomatis diisi tanpa perlu ditulis manual
static::creating(function ($model) {
if (app()->bound('currentTenant') && empty($model->tenant_id)) {
$model->tenant_id = app('currentTenant')->id;
}
});
}
}