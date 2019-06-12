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

$form ['Number'] = [
  '#type' => 'textfield',
    '#size' => 20,
  '#title' => $this->t('Enter #'),
  ];

$form['Query']= [
  '#type' => 'submit',
  '#value' => $this->t('Try'),
];

$form ['Message']= [
    '#type' => 'html_tag',
    '#tag' => 'p',
    '#value' => $this->t('xxxxx Front End xxxxx'),
];


$form['a']= [
    '#type' => 'range',
    '#title' => $this->t('1st #'),
];

$form['b']= [
    '#type' => 'range',
    '#title' => $this->t('2nd #'),
];

$form['c']= [
    '#type' => 'textfield',
    '#size' => 20,
    '#title' => $this->t('Answer'),
];

$form['btn']= [
    '#type' => 'button',
    '#value' => $this->t('Click!'),
];


	return $form;
 }


/**
   * {@inheritdoc}
   */

public function validateForm(array &$form, FormStateInterface $form_state) {
    $input1 = $form_state->getValue('Number');

    $invalidData = $this->inValid($input1);

    if ($invalidData) {
        $msg1 = 'Please enter a whole number between 1 and 11';
        $form_state->setErrorByName('Number',$msg1);
    }
}

Private function inValid($num){
    $intTest = ((floatval($num) - intval($num)) <> 0);
    $rangeTest = ($num > 11) or ($num <1);
    return ($intTest or $rangeTest);

}

/**
   * {@inheritdoc}
   */

public function submitForm(array &$form, FormStateInterface $form_state) {

    $input2 = $form_state->getValue('Number');

    $arrPrimes = $this->primeEvaluator->selectPrimeNumbers($input2);

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