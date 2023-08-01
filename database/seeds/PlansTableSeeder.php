<?php

use Illuminate\Database\Seeder;

class PlansTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $plan = \Gerardojbaez\Laraplans\Models\Plan::create([
            'name'              => config('plans.standard.name'),
            'plan_code'         => config('plans.standard.plan_code'),
            'description'       => config('plans.standard.description'),
            'price'             => config('plans.standard.price'),
            'interval'          => config('plans.standard.interval'),
            'interval_count'    => config('plans.standard.interval_count'),
            'trial_period_days' => 0,
            'sort_order'        => 1,
            'google_product_id' => config('plans.standard.google_product_id')
        ]);

        $plan->features()->saveMany([
            new \Gerardojbaez\Laraplans\Models\PlanFeature([
                'code'       => 'LIKE',
                'value'      => 30,
                'sort_order' => 1
            ]),

            new \Gerardojbaez\Laraplans\Models\PlanFeature([
                'code'       => 'REWIND_SWIPE',
                'value'      => 0,
                'sort_order' => 2
            ]),

            new \Gerardojbaez\Laraplans\Models\PlanFeature([
                'code'       => 'PRIVACY_SHOW_DISTANCE',
                'value'      => 0,
                'sort_order' => 3
            ]),

            new \Gerardojbaez\Laraplans\Models\PlanFeature([
                'code'       => 'PRIVACY_SHOW_AGE',
                'value'      => 0,
                'sort_order' => 4
            ]),
        ]);

        $plan = \Gerardojbaez\Laraplans\Models\Plan::create([
            'name'              => config('plans.plans1.name'),
            'plan_code'         => config('plans.plans1.plan_code'),
            'description'       => config('plans.plans1.description'),
            'price'             => config('plans.plans1.price'),
            'interval'          => config('plans.plans1.interval'),
            'interval_count'    => config('plans.plans1.interval_count'),
            'trial_period_days' => 0,
            'sort_order'        => 1,
            'google_product_id' => config('plans.plans1.google_product_id'),
            'apple_product_id'  => config('plans.plans1.apple_product_id')
        ]);

        $plan->features()->saveMany([
            new \Gerardojbaez\Laraplans\Models\PlanFeature([
                'code'       => 'LIKE',
                'value'      => 'unlimited',
                'sort_order' => 1
            ]),

            new \Gerardojbaez\Laraplans\Models\PlanFeature([
                'code'       => 'REWIND_SWIPE',
                'value'      => 'unlimited',
                'sort_order' => 2
            ]),

            new \Gerardojbaez\Laraplans\Models\PlanFeature([
                'code'       => 'PRIVACY_SHOW_DISTANCE',
                'value'      => 'unlimited',
                'sort_order' => 3
            ]),

            new \Gerardojbaez\Laraplans\Models\PlanFeature([
                'code'       => 'PRIVACY_SHOW_AGE',
                'value'      => 'unlimited',
                'sort_order' => 4
            ]),
        ]);

        $plan = \Gerardojbaez\Laraplans\Models\Plan::create([
            'name'              => config('plans.plans2.name'),
            'plan_code'         => config('plans.plans2.plan_code'),
            'description'       => config('plans.plans2.description'),
            'price'             => config('plans.plans2.price'),
            'interval'          => config('plans.plans2.interval'),
            'interval_count'    => config('plans.plans2.interval_count'),
            'trial_period_days' => 0,
            'sort_order'        => 1,
            'google_product_id' => config('plans.plans2.google_product_id'),
            'apple_product_id'  => config('plans.plans2.apple_product_id')
        ]);
        $plan->features()->saveMany([
            new \Gerardojbaez\Laraplans\Models\PlanFeature([
                'code'       => 'LIKE',
                'value'      => 'unlimited',
                'sort_order' => 1
            ]),
            new \Gerardojbaez\Laraplans\Models\PlanFeature([
                'code'       => 'REWIND_SWIPE',
                'value'      => 'unlimited',
                'sort_order' => 2
            ]),

            new \Gerardojbaez\Laraplans\Models\PlanFeature([
                'code'       => 'PRIVACY_SHOW_DISTANCE',
                'value'      => 'unlimited',
                'sort_order' => 3
            ]),

            new \Gerardojbaez\Laraplans\Models\PlanFeature([
                'code'       => 'PRIVACY_SHOW_AGE',
                'value'      => 'unlimited',
                'sort_order' => 4
            ]),
        ]);

        $plan = \Gerardojbaez\Laraplans\Models\Plan::create([
            'name'              => config('plans.plans3.name'),
            'plan_code'         => config('plans.plans3.plan_code'),
            'description'       => config('plans.plans3.description'),
            'price'             => config('plans.plans3.price'),
            'interval'          => config('plans.plans3.interval'),
            'interval_count'    => config('plans.plans3.interval_count'),
            'trial_period_days' => 0,
            'sort_order'        => 1,
            'google_product_id' => config('plans.plans3.google_product_id'),
            'apple_product_id'  => config('plans.plans3.apple_product_id')
        ]);

        $plan->features()->saveMany([
            new \Gerardojbaez\Laraplans\Models\PlanFeature([
                'code'       => 'LIKE',
                'value'      => 'unlimited',
                'sort_order' => 1
            ]),
            new \Gerardojbaez\Laraplans\Models\PlanFeature([
                'code'       => 'REWIND_SWIPE',
                'value'      => 'unlimited',
                'sort_order' => 2
            ]),

            new \Gerardojbaez\Laraplans\Models\PlanFeature([
                'code'       => 'PRIVACY_SHOW_DISTANCE',
                'value'      => 'unlimited',
                'sort_order' => 3
            ]),

            new \Gerardojbaez\Laraplans\Models\PlanFeature([
                'code'       => 'PRIVACY_SHOW_AGE',
                'value'      => 'unlimited',
                'sort_order' => 4
            ]),
        ]);


//        $plan = \Gerardojbaez\Laraplans\Models\Plan::create([
//            'name'              => 'LoveLock Gold',
//            'description'       => 'Pro plan',
//            'price'             => 14.99,
//            'interval'          => 'month',
//            'interval_count'    => 1,
//            'trial_period_days' => 0,
//            'sort_order'        => 1,
//        ]);
//
//        $plan->features()->saveMany([
//            new \Gerardojbaez\Laraplans\Models\PlanFeature([
//                'code'       => 'unlimited_LIKE',
//                'value'      => 50,
//                'sort_order' => 1
//            ]),
//            new \Gerardojbaez\Laraplans\Models\PlanFeature([
//                'code'       => 'five_SUPER_LIKE_per_day',
//                'value'      => 5,
//                'sort_order' => 2
//            ]),
//            new \Gerardojbaez\Laraplans\Models\PlanFeature([
//                'code'       => 'rewind_last_swipe',
//                'value'      => 5,
//                'sort_order' => 2
//            ]),
//        ]);
//
//        $plan = \Gerardojbaez\Laraplans\Models\Plan::create([
//            'name'              => 'LoveLock Gold',
//            'description'       => 'Gold plan',
//            'price'             => 8.84,
//            'interval'          => 'month',
//            'interval_count'    => 6,
//            'trial_period_days' => 0,
//            'sort_order'        => 1,
//        ]);
//
//        $plan->features()->saveMany([
//            new \Gerardojbaez\Laraplans\Models\PlanFeature([
//                'code'       => 'unlimited_LIKE',
//                'value'      => 50,
//                'sort_order' => 1
//            ]),
//            new \Gerardojbaez\Laraplans\Models\PlanFeature([
//                'code'       => 'five_SUPER_LIKE_per_day',
//                'value'      => 5,
//                'sort_order' => 2
//            ]),
//            new \Gerardojbaez\Laraplans\Models\PlanFeature([
//                'code'       => 'rewind_last_swipe',
//                'value'      => 5,
//                'sort_order' => 2
//            ]),
//        ]);
//
//        $plan = \Gerardojbaez\Laraplans\Models\Plan::create([
//            'name'              => 'LoveLock Gold',
//            'description'       => 'Gold plan',
//            'price'             => 6.82,
//            'interval'          => 'year',
//            'interval_count'    => 1,
//            'trial_period_days' => 0,
//            'sort_order'        => 1,
//        ]);
//
//        $plan->features()->saveMany([
//            new \Gerardojbaez\Laraplans\Models\PlanFeature([
//                'code'       => 'unlimited_LIKE',
//                'value'      => 50,
//                'sort_order' => 1
//            ]),
//            new \Gerardojbaez\Laraplans\Models\PlanFeature([
//                'code'       => 'five_SUPER_LIKE_per_day',
//                'value'      => 5,
//                'sort_order' => 2
//            ]),
//            new \Gerardojbaez\Laraplans\Models\PlanFeature([
//                'code'       => 'rewind_last_swipe',
//                'value'      => 5,
//                'sort_order' => 2
//            ]),
//        ]);

    }
}
