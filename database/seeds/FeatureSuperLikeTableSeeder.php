<?php

use Illuminate\Database\Seeder;

class FeatureSuperLikeTableSeeder extends Seeder
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
                        'code'       => 'SUPER_LIKE',
                        'value'      => 1,
                        'sort_order' => 6
                    ]),
                ]);
            } else {
                $plan[$key]->features()->saveMany([
                    new \Gerardojbaez\Laraplans\Models\PlanFeature([
                        'code'       => 'SUPER_LIKE',
                        'value'      => 5,
                        'sort_order' => 6
                    ]),
                ]);
            }
        }
    }
}
