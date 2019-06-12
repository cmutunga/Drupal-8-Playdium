<?php

namespace drupal\prime_numbers\PrimeEval;
use Drupal\Core\Session\AccountProxyInterface;
class PrimeEvaluator

{
    /**
     * @var AccountProxy
     */
    protected $currentUser;

    public function __construct(AccountProxyInterface $currentUser) {
        $this->currentUser = $currentUser;
    }

    //to be used to find all prime numers between two limits
    private function discoverPrimeNumbers($lowerLimit, $upperLimit){

    }

    //two additional arguments to be used
    public function selectPrimeNumbers($num) {

        /*FIX FOR $num = 1*/
        //place holder for discoverPrimeNumbers function
        $primeNumbers = [2, 3, 5, 7, 11, 13, 17, 19, 23, 29, 31];

        $keys = array_rand($primeNumbers, $num);

        $currUser = $this->currentUser->getAccountName();

        $selection = $this->buildNewArray($keys, $primeNumbers, $currUser);
        return $selection;        
    }

    private function buildNewArray(array $arr, array $mainArr, $usr){

        $num = sizeof($arr);
        $newArray = [];

        if($num == 1) {
            $j = $arr[0];
            $k = $mainArr[$j];
            array_push($newArray, $k);
            
        } else {
            for ($i = 0; $i < $num; $i++) {
                $j = $arr[$i];
                $k = $mainArr[$j];
                array_push($newArray, $k);
            }
        }

        array_push($newArray,$usr);

        return $newArray;
    }   
}

?>


