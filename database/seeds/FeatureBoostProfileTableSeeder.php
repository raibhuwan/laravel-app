<?php

use Illuminate\Database\Seeder;

class FeatureBoostProfileTableSeeder extends Seeder
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
                        'code'       => 'BOOST_PROFILE',
                        'value'      => 0,
                        'sort_order' => 7
                    ]),
                ]);
            } else {
                $plan[$key]->features()->saveMany([
                    new \Gerardojbaez\Laraplans\Models\PlanFeature([
                        'code'       => 'BOOST_PROFILE',
                        'value'      => '1',
                        'sort_order' => 7
                    ]),
                ]);
            }
        }
    }
}
