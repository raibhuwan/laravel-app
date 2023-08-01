<?php

use Illuminate\Database\Seeder;

class FeatureCustomLocationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $plan = \Gerardojbaez\Laraplans\Models\Plan::get();

        foreach ($plan as $key => $value) {
            if ($value->price == 0) {
                $plan[$key]->features()->saveMany([
                    new \Gerardojbaez\Laraplans\Models\PlanFeature([
                        'code'       => 'CUSTOM_LOCATION',
                        'value'      => 0,
                        'sort_order' => 5
                    ]),
                ]);
            } else {
                $plan[$key]->features()->saveMany([
                    new \Gerardojbaez\Laraplans\Models\PlanFeature([
                        'code'       => 'CUSTOM_LOCATION',
                        'value'      => 'unlimited',
                        'sort_order' => 5
                    ]),
                ]);
            }
        }
    }
}
