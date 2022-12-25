<?php

namespace App\Http\Controllers;

use App\Rates;
use Exception;
use Illuminate\Support\Facades\DB;
use Carbon\CarbonPeriod;

class RatesController extends Controller
{
    public function search()
    {
        try {
            $request = request();
            $date_in = $request->input('date_in');
            $date_out = $request->input('date_out');
            $period = new CarbonPeriod($date_in, $date_out);
            $query = Rates::query();
            $query->where('date', '>=', $date_in);
            $query->where('date', '<=', $date_out);
            $query = $query->get();
            $data = collect([]);
            foreach ($period as $date) {
                $row = $query->where('date', $date->toDateString());
                if ($row->count()) {
                    $data->push($row->first());
                } else {
                    $data->push(Rates::api_data($date));
                }
            }
            return self::send_success([
                'rows' => $data
            ]);
        } catch (Exception $error) {
            return self::send_error($error->getMessage());
        }
    }
}
