<?php
require_once('DBconnect.php');
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Dashboards{
    
    function getCountAllEventsForDashboard(){
        require_once 'Events.php';
        $event=new Events();
        return $event->getCountAllEventsForDashboard();
    }
    
    function wrapChartBodyP($rec, $max, $suffix=''){
        return $this->{'wrapChartBody' . $suffix}($rec, $max);
    }
    
    function wrapAdminDashboard(){
        $content='';
        $content .='<div class="middleContent">
            <div class="title">
                <h2>Dashboard</h2>
            </div>
            <section id="wrapDashboards" class="wrapDashboards citem" data-action="wrapajaxcontent" data-context="wrapHomeDashboard"><div class="wrapajaxcontent" data-content="wrapajaxcontent"></div></section>
        </div>';
        return $content;
    }
    
    function wrapMemberSignupsChart(){
        $content='';
        $content .='<section id="wrapMemberSignups" class="wrapDashboards citem" data-action="wrapajaxcontent" data-context="wrapSignupsChart"><h1>Member Signups</h1><div class="wrapajaxcontent" data-content="wrapajaxcontent"></div></section>';
        return $content;
    }
    
    function wrapSingleDashboardBlockItem($count, $text){
        $content='';
        $content .='<li class="relative left">
                    <div class="item">
                        <div class="left"><div class="insideContent">' . $text . '</div></div>
                        <div class="right"><div class="insideContent">' . $count . '</div></div>
                    </div>
                </li>';
        return $content;
    }
    
    function wrapSingleHomeDashboardItem($count, $text){
        $content='';
        $content .='<li class="relative left">
                <div class="circlestat"><span class="abs">' . $count . '</span></div>
                <div class="bodytext"><p class="gray abs">' . $text . '</p></div>
            </li>';
        return $content;
    }
    
    function wrapHomeDashboard(){
        $rec=$this->getCountAllEventsForDashboard();
        $json=array();
        $content='';
        $content .='<div id="wrapGeneralSiteStatistic" class="wrapGeneralSiteStatistic"><ul class="relative">';
        if($rec!==false && sizeof($rec)>0){
            $content .=$this->wrapSingleHomeDashboardItem($rec->count, 'Upcoming Events');
            $content .=$this->wrapSingleHomeDashboardItem($rec->countpast, 'Past Events');
            $content .=$this->wrapSingleHomeDashboardItem($rec->rsvpyes, 'Total RSVP Yes');
            $content .=$this->wrapSingleHomeDashboardItem($rec->rsvpno, 'Total RSVP No<br />or unknow');
        }
        $content .='<div class="noFloat"></div>
        </ul></div>';
        $json['success']=1;
        $json['content']=$content;
        return $json;
    }
    
    private function wrapSingleChartBar($count1, $count2, $month, $max){
        $dateObj   = DateTime::createFromFormat('!m', $month);
        $monthName = $dateObj->format('M'); // March
        $height=($count1/$max)*100;
        $height=number_format($height, 0);
        $height1=($count2/$max)*100;
        $height1=number_format($height1, 0);
        $content='';
        $content .='<li class="relative left"><div class="labelX abs">' . strtoupper($monthName) . '</div>';
        $content .='<div class="bar abs" data-height="' . $height . '" data-count="' . $count1 . '"></div>';
        $content .=$count2!==null?'<div class="bar bar1 abs" data-height="' . $height1 . '" data-count="' . $count2 . '"></div>':'';
        $content .='</li>';
        return $content;
    }
    
    private function wrapChartBody1($rec, $max){
        $content='';
        $content .='<div class="AOAChartBodyOuter relative">';
        $content .='<div class="AOAChartBody abs">';
        $rec=$this->prepareChartArray($rec, $max);
        if($rec!==false && sizeof($rec)>0){
           $content .='<ul class="chartBody abs">';
           foreach($rec as $val){
               $content .=$this->wrapSingleChartBar($val->count, null, $val->month, $max);
           }
           $content .='<div class="noFloat"></div></ul>';
        }
        $content .='</div>';
        $content .='</div>';
        return $content;
    }
    
    private function wrapChartBody($rec, $max){
        $content='';
        $content .='<div class="AOAChartBodyOuter relative">';
        $content .='<div class="AOAChartBody abs">';
        $rec=$this->prepareChartArray($rec, $max);
        if($rec!==false && sizeof($rec)>0){
            $content .='<ul class="chartBody abs">';
            foreach($rec as $val){
                $content .=$this->wrapSingleChartBar($val->count, $val->count1, $val->month, $max);
            }
            $content .='<div class="noFloat"></div></ul>';
        }
        $content .='</div>';
        $content .='</div>';
        return $content;
    }
    
    function wrapChartLegend(){
        $content='';
        $content .='<div class="chartLegend abs">';
        $content .='<div class="right grayish">Organization Users</div><div class="right redish">Total Users</div>';
        $content .='<div class="noFloat"></div></div>';
        return $content;
    }
    
    function wrapSignupsChart(){
        $rec=$this->getCountAllSignupUsersForLastMonths(12);
        $json=array();
        $content='';
        $content .='<div id="wrapSignupsChart" class="wrapSignupChart">';
        if($rec!==false && sizeof($rec)>0){
           $a=array();
           while($val=$rec->fetch(PDO::FETCH_OBJ)){
               $rec1[]=$val;
               $a[]=$val->count;
           }
           $max=max($a);
           $max1=50;
           do{
               $max1 *=10;
           }while($max-$max1>0);
           if($max-50<=0) $max=50;
           $content .=$this->wrapChartLegend();
           $content .=$this->wrapChartBody($rec1, $max1);
        }
        $content .='</div>';
        $json['success']=1;
        $json['content']=$content;
        return $json;
    }
    
    function prepareChartArray($rec, $max){
        $rec1=$rec2=array();
        foreach($rec as $val){
            $rec1[$val->month]=$val;
        }
        
        $k=date('n');
        $y=date('Y');
        for($i=1; $i<=$max; $i++){
            if($k<=0){
                $k=12;
                $y--;
            }
            if(true!==array_key_exists($k, $rec1)){
                $a=new stdClass();
                $a->count=0;
                $a->count1=0;
                $a->month=$k;
                $a->year=$y;
                $rec2[$k]=$a;
            }else{
                $rec2[$k]=$rec1[$k];
            }
            $k--;
        }
        ksort($rec2);
        return $rec2;
    }
}
