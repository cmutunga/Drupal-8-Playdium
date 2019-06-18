<?php 

namespace drupal\prime_numbers\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use drupal\prime_numbers\PrimeEval\PrimeEvaluator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Messenger\MessengerInterface;

class PrimesForm extends FormBase {

/**
   * {@inheritdoc}
   */

    private $primeEvaluator;
    protected $messenger;

    public function __construct(PrimeEvaluator $primeEvaluator, MessengerInterface $messenger)
    {
        $this->primeEvaluator = $primeEvaluator;
        $this->messenger = $messenger;
    }

    public static function create (ContainerInterface $container) {
    $primeEvaluator = $container->get('primes_evaluator');
    $messenger = $container->get('messenger');
    return new static($primeEvaluator, $messenger);

    }

    public function getFormId() {
        return 'primes_form';
    }

public function buildForm(array $form, FormStateInterface $form_state) {

$form['#attached']['library'][]= 'prime_numbers/simple-math';

$form ['Message1']= [
    '#type' => 'html_tag',
    '#tag' => 'p',
    '#value' => $this->t('-- Find some prime numbers --') .'<br>'.$this->t('-- These elements respond to backend PHP Code --'),
];

$form['backend'] = [
    '#type' => 'fieldset',
    '#title' => $this->t('Back End'),
    ];

//form elements that respond to backend drupal PHP code
$form ['backend']['Number'] = [
    '#type' => 'textfield',
    '#size' => 10,
    '#title' => $this->t('How many prime #s -- upto 5'),
];
$form ['backend']['UpperLimit'] = [
    '#type' => 'textfield',
    '#size' => 10,
    '#title' => $this->t('To ? -- upto 1000'),
    ];
$form ['backend']['LowerLimit'] = [
    '#type' => 'textfield',
    '#size' => 10,
    '#title' => $this->t('From ? -- upto 996'),
];
$form ['backend']['Query']= [
    '#type' => 'submit',
    '#value' => $this->t('Try'),
];


$form ['Message2']= [
    '#type' => 'html_tag',
    '#tag' => 'p',
    '#value' => '<br>'.'<br>'.$this->t('-- Use slider, add 2 numbers --') .'<br>'.$this->t('-- The elements below respond to frontend JS Code --'),
];

$form['frontend'] = [
    '#type' => 'fieldset',
    '#title' => $this->t('Front End'),
];

//form elements that respond to frontend JS code
$form ['frontend']['a']= [
    '#type' => 'range',
    '#title' => $this->t('1st #'),
];

$form ['frontend']['b']= [
    '#type' => 'range',
    '#title' => $this->t('2nd #'),
];

$form ['frontend']['c']= [
    '#type' => 'textfield',
    '#size' => 16,
    '#title' => $this->t('Answer'),
];

$form ['frontend']['btn']= [
    '#type' => 'button',
    '#value' => $this->t('Click!'),
];

	return $form;
 }


/**
   * {@inheritdoc}
   */

public function validateForm(array &$form, FormStateInterface $form_state) {

    $inputs = ['nm'=> $form_state->getValue('Number'), 'ul'=> $form_state->getValue('UpperLimit'), 'll'=> $form_state->getValue('LowerLimit')];

    $i=0;
    foreach ($inputs as $input){
        switch($i){
            case 0:
                $element = 'Number';
                $expected = 5;
                Break;
            case 1:
                $element = 'UpperLimit';
                $expected = 1000;
                Break;
            case 2:
                $element = 'LowerLimit';
                $expected = 996;
                Break;
        }
        $testNumPop=$this->isPopulated($input);
        $msg = $testNumPop[1];
        if ($testNumPop[0] == FALSE) {
            $form_state->setErrorByName($element,$msg);
        }

        $testNumInt=$this->isInteger($input);
        $msg = $testNumInt[1];
        if ($testNumInt[0] == FALSE) {
            $form_state->setErrorByName($element,$msg);
        }

        $testNumRange=$this->isReasonableRange($input,$expected);
        $msg = $testNumRange[1];
        if ($testNumRange[0] == FALSE) {
            $form_state->setErrorByName($element,$msg);
        }

        $i++;
    }

    $comparisonMsg = 'Upper limit should be greater than lower limit';
    if ($inputs['ul'] <= $inputs['ll'] ){
        $form_state->setErrorByName('LowerLimit',$comparisonMsg);
    }

}

//ensures that all fields are populated
Private function isPopulated ($val){

    if ($val == NULL) {
      $msg = 'Please populate all fields';
      $populated = FALSE;
    }
    $isValid = [$populated, $msg];
    return $isValid;
}

//ensures that all fields are positive integers
Private function isInteger ($val){
    //a hack
    $isInt = ctype_digit(strval($val));
    $msg = 'Please enter integer values';
    $isValidInt = [$isInt, $msg];
    return $isValidInt;
}


//ensures that inputs are within reasonable range, range between 1 and 1000, # of primes requested is less than 5 and ..
//'to' field is greater than 'from' field
Private function isReasonableRange ($set, $expected){

    if($set>$expected){
        $msg = 'Upto 5 prime-numbers between (1 upto 996) and 1000 please, lower limit not exceeding 996';
        $range = FALSE;
        $reasonable = [$range,$msg];
        return $reasonable;
    }

    if ($set<1){
            $msg = 'Only positive numbers';
            $range = FALSE;
            $reasonable = [$range,$msg];
            return $reasonable;
        }

}



/**
   * {@inheritdoc}
   */

public function submitForm(array &$form, FormStateInterface $form_state) {

    $form_state->clearErrors();

    $inputs = ['nm'=> $form_state->getValue('Number'), 'ul'=> $form_state->getValue('UpperLimit'), 'll'=> $form_state->getValue('LowerLimit')];
    $numPrimes = $inputs['nm'];
    $upperLmt = $inputs['ul'];
    $lowerLmt = $inputs ['ll'];


    $arrPrimes = $this->primeEvaluator->selectPrimeNumbers($numPrimes,$lowerLmt,$upperLmt);

    $loginUsr = array_pop($arrPrimes);

    $msg2 = $this->craftMessage($arrPrimes,$loginUsr);
    $this->messenger->addMessage($msg2);


}

private function craftMessage(array $msgData, $user) {
    $num = sizeof($msgData);
    $aMsg = 'Hello '.$user. ', here are our selected primes: ';

    for ($index = 0; $index<$num; $index++) {
      $prime = $msgData[$index];
      $aMsg = $aMsg.$prime.',';
    }
    return $aMsg;
}

}

?>