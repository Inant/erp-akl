<?php
namespace App\Http\Controllers\Master;

use App\Models\MSequence;
use App\Models\Site;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class MSequenceController extends Controller
{
    public function generateTransactionNumber(Request $request)
    {
        $request_array = $request->all();
        $transaction_code = array_key_exists('transaction_code',$request_array) ? $request_array['transaction_code'] : null;
        $period_year = array_key_exists('period_year',$request_array) ? $request_array['period_year'] : null;
        $period_month = array_key_exists('period_month',$request_array) ? $request_array['period_month'] : null;
        $site_id = array_key_exists('site_id',$request_array) ? $request_array['site_id'] : null;
        
        if($transaction_code == null || $period_year == null || $period_month == null || $site_id == null){
            return response()->json(
                [
                    'error' => true, 
                    'message' => 'Please fill all parameter {transaction_code, period_year, period_month, site_id}',
                    'data' => [$transaction_code,  $period_year, $period_month, $site_id]
                ]
            );
        } else {
            // get site code
            $site_code = DB::select('
                SELECT code FROM sites WHERE id = ?
            ', [$site_id])[0]->code;

            // get tx code from m_references
            $trx_value = DB::select('
                SELECT value FROM m_references WHERE code = ?
            ', [$transaction_code]) != null ?
            (DB::select('
                SELECT value FROM m_references WHERE code = ?
            ', [$transaction_code])[0]->value) : $transaction_code;


            $m_sequence = MSequence::where('seq_code', $transaction_code)
                ->where('period_year', $period_year)
                ->where('period_month', $period_month)
                ->where('site_id', $site_id)
                ->get();

            if(count($m_sequence) > 0){
                $new_seq_no = ($m_sequence[0]['seq_no'] + 1);

                //Update m_sequences
                $object = MSequence::findOrFail($m_sequence[0]['id']);
                $object->update(['seq_no' => $new_seq_no]);

                $gen_seq_no = '';
                $max_lenght_seq_no = $m_sequence[0]['seq_length'] - strlen((string)$new_seq_no);
                for($i = 0; $i < $max_lenght_seq_no; $i++){
                    $gen_seq_no .= '0';
                }
                $gen_seq_no .= (string)$new_seq_no;
                $period_year = substr($period_year,2,2);

                $period_month = strlen((string)$period_month) < 2 ? ('0'.(string)$period_month) : $period_month;
                $transaction_number = 'SBY-'.$trx_value.'-'.$period_month.'-'.$period_year.'-'.$gen_seq_no;
            } else {
                $new_seq_no = 1;

                //Insert m_sequences
                $m_sequence = [
                    'seq_code' => $transaction_code,
                    'period_year' => $period_year,
                    'period_month' => $period_month,
                    'site_id' => $site_id,
                    'seq_length' => 3,
                    'seq_no' => $new_seq_no
                ];
                $object = MSequence::create($m_sequence);

                $gen_seq_no = '';
                $max_lenght_seq_no = $m_sequence['seq_length'] - strlen((string)$new_seq_no);
                for($i = 0; $i < $max_lenght_seq_no; $i++){
                    $gen_seq_no .= '0';
                }
                $gen_seq_no .= (string)$new_seq_no;
                $period_year = substr($period_year,2,2);

                $period_month = strlen((string)$period_month) < 2 ? ('0'.(string)$period_month) : $period_month;
                $transaction_number = 'SBY-'.$trx_value.'-'.$period_month.'-'.$period_year.'-'.$gen_seq_no;
            }


            return response()->json(
                [
                    'error' => false, 
                    'message' => '',
                    'data' => ['transaction_number' => $transaction_number]
                    // 'data' => $m_sequence == null ? 'null' : $m_sequence[0]
                ]
            );
        }
    }
    public function generateTransactionNumberCRM(Request $request)
    {
        $request_array = $request->all();
        $transaction_code = array_key_exists('transaction_code',$request_array) ? $request_array['transaction_code'] : null;
        $period_year = array_key_exists('period_year',$request_array) ? $request_array['period_year'] : null;
        $period_month = '01';
        $site_id = array_key_exists('site_id',$request_array) ? $request_array['site_id'] : null;
        
        
        // $period_year = $request->all()['period_year'];
        // $period_month = $request->all()['period_month'];
        if($transaction_code == null || $period_year == null || $period_month == null || $site_id == null){
            return response()->json(
                [
                    'error' => true, 
                    'message' => 'Please fill all parameter {transaction_code, period_year, period_month, site_id}',
                    'data' => [$transaction_code,  $period_year, $period_month, $site_id]
                ]
            );
        } else {
            $m_sequence = MSequence::where('seq_code', $transaction_code)
                ->where('period_year', $period_year)
                ->where('period_month', $period_month)
                ->where('site_id', $site_id)
                ->get();

            if(count($m_sequence) > 0){
                $new_seq_no = ($m_sequence[0]['seq_no'] + 1);

                //get Site Code
                $site = Site::select('code') -> where('id',$m_sequence[0]['site_id']) -> first();
                $site_code = $site['code'];

                //Update m_sequences
                // $object = MSequence::findOrFail($m_sequence[0]['id']);
                // $object->update(['seq_no' => $new_seq_no]);

                $gen_seq_no = '';
                $max_lenght_seq_no = $m_sequence[0]['seq_length'] - strlen((string)$new_seq_no);
                for($i = 0; $i < $max_lenght_seq_no; $i++){
                    $gen_seq_no .= '0';
                }
                $gen_seq_no .= (string)$new_seq_no;
                $period_year = substr($period_year,2,2);

                $period_month = strlen((string)$period_month) < 2 ? ('0'.(string)$period_month) : $period_month;
                $transaction_number = $site_code .'/'.$transaction_code.'/'.$gen_seq_no.'/'.$period_year;
            } else {
                $new_seq_no = 1;

                //Insert m_sequences
                $m_sequence = [
                    'seq_code' => $transaction_code,
                    'period_year' => $period_year,
                    'period_month' => $period_month,
                    'site_id' => $site_id,
                    'seq_length' => 3,
                    'seq_no' => $new_seq_no
                ];
                $object = MSequence::create($m_sequence);
                
                //get Site Code
                $site = Site::select('code') -> where('id',$site_id) -> first();
                $site_code = $site['code'];

                $gen_seq_no = '';
                $max_lenght_seq_no = $m_sequence['seq_length'] - strlen((string)$new_seq_no);
                for($i = 0; $i < $max_lenght_seq_no; $i++){
                    $gen_seq_no .= '0';
                }
                $gen_seq_no .= (string)$new_seq_no;
                $period_year = substr($period_year,2,2);

                $period_month = strlen((string)$period_month) < 2 ? ('0'.(string)$period_month) : $period_month;
                $transaction_number = $site_code .'/'.$transaction_code.'/'.$gen_seq_no.'/'.$period_year;
            }


            return response()->json(
                [
                    'error' => false, 
                    'message' => '',
                    'data' => ['transaction_number' => $transaction_number]
                    // 'data' => $m_sequence == null ? 'null' : $m_sequence[0]
                ]
            );
        }
    }

    public function updateTransactionNumberCRM(string $trxcode)
    {
        $transaction_code = substr($trxcode,3,3);
        $seq_no = (int)(substr($trxcode,6,3));
        $period_year = '20'.substr($trxcode,9,2);;
        $period_month = '01';

        $site = Site::select('id') -> where('code',substr($trxcode,0,3)) -> first();
        $site_id = $site['id'];
        
        $m_sequence = MSequence::where('seq_code', $transaction_code)
            ->where('period_year', $period_year)
            ->where('period_month', $period_month)
            ->where('site_id', $site_id)
            ->get();
// dd($m_sequence);
        $object=null;
        //Update m_sequences
        if((int)$m_sequence[0]['seq_no']<=$seq_no)
        {
            $object = MSequence::findOrFail($m_sequence[0]['id']);
            $object->update(['seq_no' => (int)$m_sequence[0]['seq_no']+1]);
        }

        return response()->json(
            [
                'error' => false, 
                'message' => '',
                'data' => $object
                // 'data' => $m_sequence == null ? 'null' : $m_sequence[0]
            ]
        );
    }
}
