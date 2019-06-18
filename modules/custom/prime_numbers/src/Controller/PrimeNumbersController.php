<?php

namespace drupal\prime_numbers\Controller;

use Drupal\Core\Controller\ControllerBase;

/*use Drupal\Core\Form\FormBuilder;*/
/*use Drupal\prime_numbers\PrimeEval\PrimeEvaluator;*/

use drupal\prime_numbers\PrimeEval\PrimeEvaluator;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PrimeNumbersController extends ControllerBase
{

    private $primeEvaluator;
    /*protected $formBuilder;*/

    public function __construct(PrimeEvaluator $primeEvaluator)
    {
       $this->primeEvaluator = $primeEvaluator;
       /*$this->formBuilder = $formBuilder;*/
    }

    public function isItPrime($count)
    {
        //to add two more arguments to cover upper and lower discovery limits
        $arrPrimes = $this->primeEvaluator->selectPrimeNumbers($count, 2, 200);

        $num = sizeof($arrPrimes);
        $usr = array_pop($arrPrimes);


        $msg = "Our primes are ";

        for($i=0;$i<($num-1);$i++){
         $msg = $msg . $arrPrimes[$i] .', ';
        }

        return [
            '#theme' => 'prime_detect',
            '#items' => $arrPrimes,
            '#user' => $usr,
            '#header' => $this -> t('Our Selected Primes'),
        ];
    }

    public static function create(ContainerInterface $container)
    {
        $primeEvaluator = $container->get('primes_evaluator');
        return new static($primeEvaluator);
    }
}

?>