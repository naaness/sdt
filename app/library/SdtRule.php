<?php
/**
 * Created by PhpStorm.
 * User: nesto_000
 * Date: 3/05/15
 * Time: 08:15 PM
 */

require_once __DIR__ . '/../../vendor/When/When.php';

use Phalcon\Mvc\User\Component;


class SdtRule extends Component{
    public function createRecurrentEvent($opt,$feSta,$feIni,$feEnd){
        $wd = '';
        $bmd = '';
        $freq = 'WEEKLY';
        if ($opt->options==1){
            $freq = 'DAILY';
        }else if ($opt->options==2){
                $wd = 'MO,TU,WE,TH,FR,SA,SU';
        }else if ($opt->options==3){
            $wd = 'MO,WE,FR';
        }else if ($opt->options==4){
            $opt->each_period=1;
            $wd = 'TU,TH';
        }else if ($opt->options==5){
            if($opt->day_L==1){
                $wd.=',MO';
            }
            if($opt->day_M==1){
                $wd.=',TU';
            }
            if($opt->day_X==1){
                $wd.=',WE';
            }
            if($opt->day_J==1){
                $wd.=',TH';
            }
            if($opt->day_V==1){
                $wd.=',FR';
            }
            if($opt->day_S==1){
                $wd.=',SA';
            }
            if($opt->day_D==1){
                $wd.=',SU';
            }
            if($wd!=''){
                $wd=substr($wd,1,strlen($wd)-1);
                $wd=';BYDAY='.$wd;
            }
        }else if ($opt->options==6){
            $freq = 'MONTHLY';
            if($opt->month_week==1){
                $wd = null;
                $bmd = ';BYMONTHDAY='.date("d", $feSta); //parseInt(moment.utc(feSta).format('DD'))];
            }else if($opt->month_week==2){
                $th = -1;
                if($opt->day_position>0){
                    $th=$opt->day_position;
                }
                $d = (int) date("w", $feSta);//moment.utc(feSta).format('E');
                if ($d==1){
                    $wd=$th.'MO';
                }else if ($d==2){
                    $wd=$th.'TU';
                }else if ($d==3){
                    $wd=$th.'WE';
                }else if ($d==4){
                    $wd=$th.'TH';
                }else if ($d==5){
                    $wd=$th.'FR';
                }else if ($d==6){
                    $wd=$th.'SA';
                }else if ($d==0){
                    $wd=$th.'SU';
                }
                if($wd!=''){
                    $wd=';BYDAY='.$wd;
                }
            }
        }else if ($opt->options==6){
            $freq = 'YEARLY';
        }

        if($opt->end_day!='0000-00-00'){
            $feFin = new DateTime($opt->end_day);
            $endDate = $feEnd;
            if($this->diffDate($feFin,$feEnd)>=1){
                $endDate = $feFin;
            }
            // Create a rule:
//            var opcions = {
//                freq: freq,
//            dtstart: feSta,
//            until: endDate,
//            count: 100000,
//            interval: parseInt(opt.each_period),
//            byweekday: wd,
//            bymonthday:bmd

            $opcions = 'FREQ='.$freq.';INTERVAL='.$opt->each_period.';COUNT=100000'.$wd.$bmd;
            $r = new When();
            $r->startDate($feSta)
                ->rrule($opcions)
                ->until($endDate)
                ->generateOccurrences();
        }else{
            // Create a rule:
//            var opcions = {
//                freq: freq,
//            dtstart: feSta,
//            count: opt.repeat_interval,
//            interval: parseInt(opt.each_period),
//            byweekday: wd,
//            bymonthday:bmd
            $opcions = 'FREQ='.$freq.';INTERVAL='.$opt->each_period.';COUNT='.$opt->repeat_interval.$wd.$bmd;
        }
        $datos = array();
        foreach ($r->occurrences as $ocurr)
        {
            $datos[] = date_format($ocurr,'Y-m-d');
        }
        return $datos;
    }
    public function diffDate($start_day, $end_day){
        $interval =($this->toMktime($start_day) - $this->toMktime($end_day))/(3600*24);
        return round($interval, 0, PHP_ROUND_HALF_UP);
    }
    public function toMktime($date){
        return mktime(0,0,0,date_format($date,'n'),date_format($date,'j'),date_format($date,'Y'));
    }
}