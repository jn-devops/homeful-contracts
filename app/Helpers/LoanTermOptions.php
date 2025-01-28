<?php

namespace App\Helpers;

class LoanTermOptions
{
    public Array $data = [];
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->data = [
            [
                'term_1' => '1 - 36',
                'term_2' => '37 - 348',
                'term_3' => '349 - 360',
                'months_term' => 360,
                'loanable_years' => 30,
            ],
            [
                'term_1' => '1 - 36',
                'term_2' => '37 - 336',
                'term_3' => '337 - 348',
                'months_term' => 348,
                'loanable_years' => 29,
            ],
            [
                'term_1' => '1 - 36',
                'term_2' => '37 - 324',
                'term_3' => '325 - 336',
                'months_term' => 336,
                'loanable_years' => 28,
            ],
            [
                'term_1' => '1 - 36',
                'term_2' => '37 - 312',
                'term_3' => '313 - 324',
                'months_term' => 324,
                'loanable_years' => 27,
            ],
            [
                'term_1' => '1 - 36',
                'term_2' => '37 - 300',
                'term_3' => '301 - 312',
                'months_term' => 312,
                'loanable_years' => 26,
            ],
            [
                'term_1' => '1 - 36',
                'term_2' => '37 - 288',
                'term_3' => '289 - 300',
                'months_term' => 300,
                'loanable_years' => 25,
            ],
            [
                'term_1' => '1 - 36',
                'term_2' => '37 - 276',
                'term_3' => '277 - 288',
                'months_term' => 288,
                'loanable_years' => 24,
            ],
            [
                'term_1' => '1 - 36',
                'term_2' => '37 - 264',
                'term_3' => '265 - 276',
                'months_term' => 276,
                'loanable_years' => 23,
            ],
            [
                'term_1' => '1 - 36',
                'term_2' => '37 - 252',
                'term_3' => '253 - 264',
                'months_term' => 264,
                'loanable_years' => 22,
            ],
            [
                'term_1' => '1 - 36',
                'term_2' => '37 - 240',
                'term_3' => '241 - 252',
                'months_term' => 252,
                'loanable_years' => 21,
            ],
            [
                'term_1' => '1 - 36',
                'term_2' => '37 - 228',
                'term_3' => '229 - 240',
                'months_term' => 240,
                'loanable_years' => 20,
            ],
            [
                'term_1' => '1 - 36',
                'term_2' => '37 - 216',
                'term_3' => '217 - 228',
                'months_term' => 228,
                'loanable_years' => 19,
            ],
            [
                'term_1' => '1 - 36',
                'term_2' => '37 - 204',
                'term_3' => '205 - 216',
                'months_term' => 216,
                'loanable_years' => 18,
            ],
            [
                'term_1' => '1 - 36',
                'term_2' => '37 - 192',
                'term_3' => '193 - 204',
                'months_term' => 204,
                'loanable_years' => 17,
            ],
            [
                'term_1' => '1 - 36',
                'term_2' => '37 - 180',
                'term_3' => '181 - 192',
                'months_term' => 192,
                'loanable_years' => 16,
            ],
            [
                'term_1' => '1 - 36',
                'term_2' => '37 - 168',
                'term_3' => '169 - 180',
                'months_term' => 180,
                'loanable_years' => 15,
            ],
            [
                'term_1' => '1 - 36',
                'term_2' => '37 - 156',
                'term_3' => '157 - 168',
                'months_term' => 168,
                'loanable_years' => 14,
            ],
            [
                'term_1' => '1 - 36',
                'term_2' => '37 - 144',
                'term_3' => '145 - 156',
                'months_term' => 156,
                'loanable_years' => 13,
            ],
            [
                'term_1' => '1 - 36',
                'term_2' => '37 - 132',
                'term_3' => '133 - 144',
                'months_term' => 144,
                'loanable_years' => 12,
            ],
            [
                'term_1' => '1 - 36',
                'term_2' => '37 - 120',
                'term_3' => '121 - 132',
                'months_term' => 132,
                'loanable_years' => 11,
            ],
            [
                'term_1' => '1 - 36',
                'term_2' => '37 - 108',
                'term_3' => '109 - 120',
                'months_term' => 120,
                'loanable_years' => 10,
            ],
            [
                'term_1' => '1 - 36',
                'term_2' => '37 - 96',
                'term_3' => '97 - 108',
                'months_term' => 108,
                'loanable_years' => 9,
            ],
            [
                'term_1' => '1 - 36',
                'term_2' => '37 - 84',
                'term_3' => '85 - 96',
                'months_term' => 96,
                'loanable_years' => 8,
            ],
            [
                'term_1' => '1 - 36',
                'term_2' => '37 - 72',
                'term_3' => '73 - 84',
                'months_term' => 84,
                'loanable_years' => 7,
            ],
            [
                'term_1' => '1 - 36',
                'term_2' => '37 - 60',
                'term_3' => '61 - 72',
                'months_term' => 72,
                'loanable_years' => 6,
            ],
            [
                'term_1' => '1 - 36',
                'term_2' => '37 - 61',
                'term_3' => '62 - 48',
                'months_term' => 60,
                'loanable_years' => 5,
            ],
        ]; 
    }

    public static function getDataByMonthsTerm($months_term){
        if($months_term){
            $instance = new self();
            $collection = collect($instance->data);
            if($return = $collection->firstWhere('months_term', $months_term)){
                return $return;
            }else{
                return [];
            }
        }else{
            return [];
        }
    }

    public static function getOptions(){
        $instance = new self();
        $collection = collect($instance->data);
        $options = $collection->mapWithKeys(fn($value, $key) => [$value['months_term'] => $value['months_term'] . ' Months' ]);
        return $options->toArray();
    }

}
