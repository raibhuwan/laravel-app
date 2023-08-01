<?php

namespace App\Transformers;

use Gerardojbaez\Laraplans\Models\PlanSubscription;
use League\Fractal\TransformerAbstract;

class PlanSubscriptionTransformer extends TransformerAbstract
{
    public function transform(PlanSubscription $planSubscription)
    {
        $formattedPlanSubscription = [
            'id'                   => $planSubscription->id,
            'subscribable_id'      => $planSubscription->subscribable_id,
            'subscribable_type'    => $planSubscription->subscribable_type,
            'plan_id'              => $planSubscription->plan_id,
            'name'                 => $planSubscription->name,
            'canceled_immediately' => $planSubscription->canceled_immediately,
            'trial_ends_at'        => (string)$planSubscription->trial_ends_at,
            'starts_at'            => (string)$planSubscription->starts_at,
            'ends_at'              => (string)$planSubscription->ends_at,
            'canceled_at'          => (string)$planSubscription->canceled_at,
            'created_at'           => (string)$planSubscription->created_at,
            'updated_at'           => (string)$planSubscription->updated_at,
        ];

        return $formattedPlanSubscription;
    }
}