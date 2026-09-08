<?php

declare(strict_types=1);

namespace Hwkdo\IntranetAppFormwerk\Support;

use Illuminate\Database\Eloquent\Model;

/**
 * Legacy-Parität: Gvp::welchesGb() – Bezeichnung des Geschäftsbereichs.
 */
final class GvpBusinessUnitResolver
{
    public static function label(?Model $gvp): ?string
    {
        if ($gvp === null) {
            return null;
        }

        $kuerzel = (string) ($gvp->kuerzel ?? '');

        if ($kuerzel === 'GB') {
            return (string) $gvp->bezeichnung;
        }

        if (in_array($kuerzel, ['Stab', 'A'], true)) {
            $parent = $gvp->relationLoaded('parent') ? $gvp->parent : $gvp->parent()->first();

            return $parent ? (string) $parent->bezeichnung : null;
        }

        if (in_array($kuerzel, ['G', 'FB'], true)) {
            $parent = $gvp->relationLoaded('parent') ? $gvp->parent : $gvp->parent()->with('parent')->first();
            $grandparent = $parent?->parent;

            return $grandparent ? (string) $grandparent->bezeichnung : null;
        }

        return null;
    }
}
