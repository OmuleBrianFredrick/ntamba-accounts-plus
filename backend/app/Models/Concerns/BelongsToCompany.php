<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * Scopes every query to the current user's company (plan: "A company id on
 * every record, enforced by a global scope"). The current company comes from
 * the authenticated user; APIs set it implicitly via the token owner.
 */
trait BelongsToCompany
{
    public static function bootBelongsToCompany(): void
    {
        static::addGlobalScope('company', function (Builder $builder) {
            if ($companyId = static::currentCompanyId()) {
                $builder->where($builder->getModel()->getTable() . '.company_id', $companyId);
            }
        });

        static::creating(function ($model) {
            if (empty($model->company_id) && $companyId = static::currentCompanyId()) {
                $model->company_id = $companyId;
            }
        });
    }

    public static function currentCompanyId(): ?int
    {
        // No auth context (console/seed/tests) => no scope, so seeds can run.
        return auth()->check() ? auth()->user()->company_id : null;
    }

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }
}
