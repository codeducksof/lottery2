<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Lotto_main;
use App\Lotto_user;
use DB;
use File;
use Arr;
class LotteryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return "have_user";
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function testUsers(Request $request)
    {
        return "have_user";
    }

    public function sendLotto(Request $request)
    {
        return "success";
    }

  
    public function showWinComment(Request $request)
    {
        $historyPlay = array();
        $historyPlaySub = array();
        $AllhistoryPlays = DB::select("SELECT 
                                        `ID`, 
                                        `main_id`, 
                                        `user_id`, 
                                        `username`, 
                                        `tel`, 
                                        `lotto_number`, 
                                        `nickname`, 
                                        `comment`, 
                                        `comment_datetime` as date, 
                                        `datetime_prediction`, 
                                        `result`, 
                                        `answer_result`, 
                                        `F_Active`, 
                                        `created_at`, 
                                        `updated_at` 
                                    FROM `lotto_users` 
                                    WHERE F_Active = 'Y'
                                    AND comment <> ''
                                    order by comment_datetime desc");
        $AllhistoryPlays_count = count($AllhistoryPlays);
        $i = 3;
        $index = 0;
        $indexfinal = 0;
        foreach ($AllhistoryPlays as $AllhistoryPlay) {
            
            
            if($index <= $i)
            {
                array_push($historyPlaySub, $AllhistoryPlay);
                $index++;
                
                if(($indexfinal+1) == $AllhistoryPlays_count)
                {
                     array_push($historyPlay, $historyPlaySub);
                }
            }
            else
            {
               
                array_push($historyPlay, $historyPlaySub);
                $historyPlaySub = array();
                array_push($historyPlaySub, $AllhistoryPlay);
                $index = 1;
            }
           $indexfinal++;
            
        }
         return response()->json($historyPlay);
    }
    
    

    public function saveWinComment(Request $request)
    {
        $status_text = '';
        $status = '';
        
        $chk_comment = DB::select("SELECT *
                                    FROM `lotto_users`
                                    WHERE ID = ".$request->customer_answer_id."
                                    AND comment <> ''");
        $count_comment = count($chk_comment);    
        if($count_comment <= 0)
        {

            $historyLists = DB::update("UPDATE `lotto_users` 
                                    SET `comment`= '".$request->comment_text."',
                                    `comment_datetime`='".date("Y-m-d H:i:s")."',
                                    `updated_at`='".date("Y-m-d H:i:s")."'
                                    WHERE ID = ".$request->customer_answer_id."");
            $status_text = '';
            $status = 'SUCCESS';

        }
        else
        {
            $status_text = 'COMMENT_ALREADY';
            $status = 'ERROR';
        }
        

        

        return response()->json([
            'status_text' => $status_text,
            'status' => $status,
        ]);
    }
    
    public function chk_answer(Request $request)
    {
        $historyLists = DB::select("SELECT t1.id , t1.username as user , t1.tel , t1.nickname, t2.date_award as lottoDate , t2.around as round , t1.lotto_number as answer,t2.`lotto_number_award` as result,t1.comment , t1.result as answer_result
                                    FROM `lotto_users` t1
                                    left join lotto_main t2 on t2.ID = t1.main_id
                                    WHERE t1.username = '".$request->username."'
                                    AND t1.tel = '".$request->tel."'");

        return $historyLists;
    }
    
    public function getPersonalStatistic(Request $request)
    {
        $historyLists = DB::select("SELECT t2.date_award as date , t2.around as round , t1.lotto_number as answer,t1.`result` as is_win , t1.answer_result
                                    FROM `lotto_users` t1
                                    left join lotto_main t2 on t2.ID = t1.main_id
                                    WHERE t1.user_id = ".$request->customer_id."");
        $total = 0;
        $win_total = count($historyLists);
        $array_wins = array('win_3','win_2','win_toss');
        
        foreach ($historyLists as $historyList) {
            if (in_array($historyList->is_win, $array_wins))
            {
                $total++;
            }
        }
        
        $totalPlay = array();
        $totalPlay['total'] = $total;
        $totalPlay['win_total'] = $win_total;
        
        return response()->json([
            'historyList' => $historyLists,
            'totalPlay' => $totalPlay,
        ]);
    }
   
    public function getUserHistroy(Request $request)
    {

        $Next_arounds = DB::select("SELECT T2.ID , T1.time_award  ,SUBSTRING(T2.date_award, 1, 16) AS date_award , T2.around , T2.lotto_number_award
                    FROM lotto_setting T1 
                    LEFT JOIN lotto_main T2 ON T2.ID_setting = T1.ID
                    WHERE T1.F_Active = 'Y'
                    AND T2.F_Active = 'Y'
                    AND T1.time_award = '$request->date'
                    order by T2.date_award ASC");
                    
                    
        $Next_around_count = count($Next_arounds);
        
        $historyPlay = '';
        $getTotalLottoResult = '';
        $historyPlay = array();
        $historyPlaySub = array();
        if($Next_around_count > 0)
        {

            
           
            
            $id = array();
            foreach ($Next_arounds as $Next_around) {
                $getTotalLottoResult .= ' <span class="badge badge-primary">รอบที่ '.$Next_around->around.' : '. $Next_around->lotto_number_award.'</span> ';
                array_push($id, $Next_around->ID);
            }
            
            
            
            $AllhistoryPlays = DB::select("SELECT  lotto_users.`ID`,  lotto_users.`main_id`,  lotto_users.`user_id`,  lotto_users.`username`,CONCAT(SUBSTRING( lotto_users.`tel`, 1, 3),'xxxx',SUBSTRING( lotto_users.`tel`, 8, 3)) as `tel` ,  lotto_users.`lotto_number`,  lotto_users.`nickname`,  lotto_users.`comment`,  lotto_users.`comment_datetime`,  lotto_users.`datetime_prediction`,  lotto_users.`F_Active`, SUBSTRING(TIME( lotto_users.`created_at`), 1, 5) as `created_at`,  lotto_users.`updated_at` ,
                                            lotto_main.around,lotto_main.lotto_number_award
                                            FROM `lotto_users` 
                                            LEFT JOIN lotto_main on lotto_main.ID = lotto_users.main_id
                                            WHERE lotto_users.F_Active = 'Y' 
                                            AND  lotto_users.main_id IN (".implode(',',$id).")
                                            order by  lotto_users.ID DESC");
            $AllhistoryPlays_count = count($AllhistoryPlays);
            $i = 9;
            $index = 0;
            $indexfinal = 0;
            if($AllhistoryPlays_count > 0)
            {
                
            
                foreach ($AllhistoryPlays as $AllhistoryPlay) {
                    
                    if($AllhistoryPlay->lotto_number == $AllhistoryPlay->lotto_number_award)
                    {
                        $AllhistoryPlay->result = 'win_3';
           
                    }elseif(substr($AllhistoryPlay->lotto_number, 1) == substr($AllhistoryPlay->lotto_number_award, 1))
                    {
                        $AllhistoryPlay->result = 'win_2';
                
                    }else
                    {
                        $n1 = '';
                        $n2 = '';
                        $n3 = '';
                        $n4 = '';
                        $n5 = '';
                        $n6 = '';
                        $number = $AllhistoryPlay->lotto_number_award;
                        $a = substr("$number", -3, 1);   
                        $b = substr("$number", -2, 1);   
                        $c = substr("$number", -1); 
                        	
                        	if(($a == $b)||($a == $c)||($b == $c)){
                        		if($a == $b){
                        			$n1 = $a.$a.$c;
                        			$n2 = $a.$c.$a;
                        			$n3 = $c.$a.$a;
                        		}elseif($a == $c){
                        			$n1 = $a.$b.$a;
                        			$n2 = $a.$a.$b;
                        			$n3 = $b.$a.$a;
                        		}else{
                        			$n1 = $a.$b.$b;
                        			$n2 = $b.$b.$a;
                        			$n3 = $b.$a.$b;
                        		}
                        	}else{
                        	 $n1 = $a.$b.$c;
                        	 $n2 = $a.$c.$b;
                        	 $n3 = $b.$a.$c; 
                        	 $n4 = $b.$c.$a; 
                        	 $n5 = $c.$a.$b; 
                        	 $n6 = $c.$b.$a; 
                        	}
                
                        $txtnumber = $n1.','.$n2.','.$n3.','.$n4.','.$n5.','.$n6;
                        $txtnumber = str_replace(",,",",",$txtnumber);
                        $txtnumber = str_replace(",,",",",$txtnumber);
                        
                        $txtnumber = explode(',', $txtnumber);
                       
                        if (in_array($AllhistoryPlay->lotto_number, $txtnumber))
                        {
                             $AllhistoryPlay->result = 'win_toss';
                             
                        }
                        else
                        {
                            $AllhistoryPlay->result = '-';
                        }
                        
                 
                    }
                    
                    
                    if($index <= $i)
                    {
                        
                        array_push($historyPlaySub, $AllhistoryPlay);
                        
                        $index++;
                        
                        if(($indexfinal+1) == $AllhistoryPlays_count)
                        {
                            
                             array_push($historyPlay, $historyPlaySub);
                      
                        }
                    }
                    else
                    {
                       
                        array_push($historyPlay, $historyPlaySub);
                        $historyPlaySub = array();
                        array_push($historyPlaySub, $AllhistoryPlay);
                        $index = 1;
                    }
                   $indexfinal++;
                    
                }
            }
        }
        
        $historyWinner = array();
        $historyWinnerSub = array();
        $historyWinnerMain = array();
        $historyWinnerMain2 = array();
        $historyWinnerMain['limit_per_page'] = 10;
        $historyWinnerMain['page'] = '';
        
        $lottoDate = $request->date;

        $historyPlay_count = count($historyPlay);
        if($historyPlay_count > 0)
        {
            foreach ($historyPlay as $historyPla) {
                 $filteredArray = Arr::where($historyPla, function ($value, $key) {
                    return $value->result != '-';
                });
                array_push($historyWinner, $filteredArray);
            }
        }
        

        $AllhistoryPlays_count = count($historyWinner);
        $i = 9;
        $index = 0;
        $indexfinal = 0;
        if($AllhistoryPlays_count > 0)
        {
            foreach ($historyWinner as $historyWinne) {
                if($index <= $i)
                {
                    
                    array_push($historyWinnerSub, $historyWinne);
                    
                    $index++;
                    
                    if(($indexfinal+1) == $AllhistoryPlays_count)
                    {
                        
                         array_push($historyWinnerMain, $historyWinnerSub);
                  
                    }
                }
                else
                {
                   
                    array_push($historyWinnerMain, $historyWinnerSub);
                    $historyWinnerSub = array();
                    array_push($historyWinnerSub, $historyWinne);
                    $index = 1;
                }
               $indexfinal++;
                
            }
            $historyWinnerMain2 = $historyWinnerMain[0];
        }
        

        
        return response()->json([
            'historyPlay' => $historyPlay,
            'historyWinner' => $historyWinnerMain2,
            'lottoDate' => $lottoDate,
            'getTotalLottoResult' => $getTotalLottoResult,
            
        ]);
    }
    public function getAllData(Request $request)
    {
        $Pre_around = DB::select("SELECT T1.time_award  , SUBSTRING(T2.date_award, 1, 16) AS date_award, T2.around , T2.lotto_number_award
                    FROM lotto_setting T1 
                    LEFT JOIN lotto_main T2 ON T2.ID_setting = T1.ID
                    WHERE T1.F_Active = 'Y'
                    AND T2.F_Active = 'Y'
                    AND T2.lotto_number_award <> ''
                    order by T2.date_award DESC
                    LIMIT 1");
        $Pre_around_count = count($Pre_around);
        
        $lotto_number_award = '';
        $Pre_date_award =  '';
        if($Pre_around_count > 0)
        {
             $lotto_number_award = $Pre_around[0]->lotto_number_award;
            $Pre_date_award =  $Pre_around[0]->date_award;
        }
       
        
        $Next_around = DB::select("SELECT T2.ID , T1.time_award  ,SUBSTRING(T2.date_award, 1, 16) AS date_award , T2.around , T2.lotto_number_award
                    FROM lotto_setting T1 
                    LEFT JOIN lotto_main T2 ON T2.ID_setting = T1.ID
                    WHERE T1.F_Active = 'Y'
                    AND T2.F_Active = 'Y'
                    AND T2.lotto_number_award = ''
                     AND T2.date_award >= '".date("Y-m-d H:i:s")."'
                    order by T2.date_award ASC
                    LIMIT 1");
        $Next_around_count = count($Next_around);
        
        $getGuessRound = '';
        $getGuessRoundDate = '';
        $historyPlay = '';
        if($Next_around_count > 0)
        {
            $getGuessRound = $Next_around[0]->around;
            $getGuessRoundDate = $Next_around[0]->date_award;
            $historyPlay = array();
            $historyPlaySub = array();
            $AllhistoryPlays = DB::select("SELECT `ID`, `main_id`, `user_id`, `username`,CONCAT(SUBSTRING(`tel`, 1, 3),'xxxx',SUBSTRING(`tel`, 8, 3)) as `tel` , `lotto_number`, `nickname`, `comment`, `comment_datetime`, `datetime_prediction`, `F_Active`, SUBSTRING(TIME(`created_at`), 1, 5) as `created_at`, `updated_at` 
                                            FROM `lotto_users` WHERE F_Active = 'Y' AND main_id = ".$Next_around[0]->ID." order by ID DESC");
            $AllhistoryPlays_count = count($AllhistoryPlays);
            $i = 9;
            $index = 0;
            $indexfinal = 0;
            foreach ($AllhistoryPlays as $AllhistoryPlay) {
                
                
                if($index <= $i)
                {
                    array_push($historyPlaySub, $AllhistoryPlay);
                    $index++;
                    
                    if(($indexfinal+1) == $AllhistoryPlays_count)
                    {
                         array_push($historyPlay, $historyPlaySub);
                    }
                }
                else
                {
                   
                    array_push($historyPlay, $historyPlaySub);
                    $historyPlaySub = array();
                    array_push($historyPlaySub, $AllhistoryPlay);
                    $index = 1;
                }
               $indexfinal++;
                
            }
        }
        
        
     
        
        return response()->json([
            'getTimePreLottory' => $Pre_date_award,
            'preLottory' => $lotto_number_award,
            'Next_around_count' => $Next_around_count,
            'getGuessRound' => $getGuessRound,
            'getGuessRoundDate' => $getGuessRoundDate,
            'historyPlay' => $historyPlay,
            
        ]);
    }
}
