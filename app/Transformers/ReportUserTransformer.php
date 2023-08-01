<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class ReportUserTransformer extends TransformerAbstract
{
    public function transform($reportUser)
    {
        $formattedReportUser = [
            'reported_by' => $reportUser->reported_by,
            'reported_to' => $reportUser->reported_to,
            'reason'      => $reportUser->reason,
        ];

        return $formattedReportUser;
    }
}
