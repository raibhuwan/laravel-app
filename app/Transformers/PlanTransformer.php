<?php

namespace App\Transformers;

use Gerardojbaez\Laraplans\Models\Plan;
use League\Fractal\TransformerAbstract;

class PlanTransformer extends TransformerAbstract
{
    public function transform(Plan $plan)
    {
        $formattedPlan = [
            'id'                => $plan->id,
            'name'              => $plan->name,
            'description'       => $plan->description,
            'price'             => $plan->price,
            'interval'          => $plan->interval,
            'interval_count'    => $plan->interval_count,
            'trial_period_days' => $plan->trial_period_days,
            'sort_order'        => $plan->sort_order,
            'google_product_id' => $plan->google_product_id,
            'apple_product_id'  => $plan->apple_product_id,
            'created_at'        => (string)$plan->created_at,
            'updated_at'        => (string)$plan->updated_at,
        ];

        return $formattedPlan;
    }
}