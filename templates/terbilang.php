<?php
    

    function terbilang1 ($bil){
         $bil = intval($bil);
        $bilangan = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan");
         $level = array("", "Ribu", "Juta", "Milyar", "Trilyun", "Bilyun");

        if (intval($bil)>=0 and intval($bil)<=999) {
             $a = intval($bil/100);
            $b = intval(($bil%100)/10);
            $c = intval($bil%10);

            $hasil = "";
            $temp1 = "";
            switch($a){
                case 0 :
                    $temp1 = "";
                break;

                case 1 :
                    $temp1 = "Seratus";
                break;

                default :
                    $temp1 = $bilangan[$a]." Ratus";
                break;
            }

            if ($temp1!="") {
                $hasil = $hasil." ".$temp1;
            }

            // puluhan
            $temp2 = "";
            switch($b){
                case 0:
                    $temp2 = $bilangan[$c];
                break;

                case 1:
                    if($c==0){
                        $temp2 = "Sepuluh";
                    }elseif($c==1){
                        $temp2 = "Sebelas";
                    }else{
                        $temp2 = $bilangan[$c]." Belas";
                    }
                break;

                default:
                    $temp2 = $bilangan[$b]." Puluh ".$bilangan[$c];
                break;
            }

            if ($temp2!="") {
                $hasil = $hasil." ".$temp2;
            }
         }
        return $hasil;
    }

    function terbilang($n){
         $co_level = array("", "Ribu", "Juta", "Milyar", "Trilyun", "Bilyun");
         $hasil = "";
         $level = 0;
         while($n<>0){
             $tripet = $n%1000;
            $n=$n/1000;
            $temp=terbilang1($tripet);
            if ($temp!="") {
                $hasil = $temp." ".$co_level[$level]." ".$hasil;
            }
            $level++;
         }
         return $hasil;
    }
?>