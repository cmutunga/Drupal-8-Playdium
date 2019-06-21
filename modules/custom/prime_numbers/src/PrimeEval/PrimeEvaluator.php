<?php

namespace drupal\prime_numbers\PrimeEval;
use Drupal\Core\Session\AccountProxyInterface;

//this is a trivial service that selects a specified number of prime numbers between to limits. Say, select three
//prime numbers at random between 30 and 100
class PrimeEvaluator

{
    /**
     * @var AccountProxy
     */
    protected $currentUser;
    //current user service allows us to include a personalized greeting to the user
    public function __construct(AccountProxyInterface $currentUser) {
        $this->currentUser = $currentUser;
    }


    public function selectPrimeNumbers($num, $lowerLimit, $upperLimit) {
        //find all prime numbers between two numbers
        $primeNumbers = $this->discoverPrimes($lowerLimit,$upperLimit);
        //select a few keys keys at random
        $keys = array_rand($primeNumbers, $num);

        $currUser = $this->currentUser->getAccountName();

        $selection = $this->buildNewArray($keys, $primeNumbers, $currUser);
        return $selection;        
    }
    //to be used to find all prime numbers between two limits
    private function discoverPrimes ($lower, $upper){
       $arrPrimes = [];
       for ($x=$lower; $x<=$upper; $x++){
       if ($this->isItPrime($x)){
           array_push($arrPrimes, $x);
       }
       }
        return $arrPrimes;
    }
    //find out if a particular number is prime
    private function isItPrime($testNum){
        $countLimit = $testNum/2;
        $prime = TRUE;
        $x=2;

        while($x<$countLimit){
          if($testNum%$x==0){
              $prime=FALSE;
          }
        $x++;
        }
        return $prime;
    }
    //select elements from the main array of prime using indexes selected at random
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


