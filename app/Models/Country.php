<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model {

    protected static $titles = [
        'country', 'cases', 'todayCases', 'deaths',
        'todayDeaths', 'recovered', 'activeCases', 'critical',
    ];

    // Get country flag emoji by $country
    public static function generateEmoji($country)
    {
        $flag = file_get_contents(__DIR__ . '/../../data/flags.json');
        return collect(json_decode($flag, true))->firstWhere('name', $country)['emoji'];
    }

    // Get country name by $code
    public static function generateCountryName($code)
    {
        $flag = file_get_contents(__DIR__ . '/../../data/flags.json');
        return collect(json_decode($flag, true))->firstWhere('code', $code)['name'];
    }

    // Get all country data
    public static function country_data($data = null)
    {
        return collect($data)
            ->slice(9)
            ->reject(function ($item) {
                return $item[0] == 'Total:';
            })
            ->map(function ($item) {
                foreach (static::$titles as $key => $value) {
                    $data[$value] = in_array($value, ['country', 'emoji']) ? $item[$key] : intval($item[$key]);
                }
                $data['emoji'] = Country::generateEmoji($item[0]);
                return $data;
            })
            ->values()
            ->all();
    }
}