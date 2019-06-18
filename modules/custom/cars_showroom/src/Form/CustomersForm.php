<?php
namespace Drupal\cars_showroom\Form;

use drupal\cars_showroom\DBInterface\DataAccess;
use drupal\cars_showroom\DBInterface\FKValidator;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CustomersForm extends FormBase{

    Protected $dbAccess;
    Protected $fkValidator;

    public function __construct(DataAccess $dbAccess, FKValidator $fkValidator) {

        $this->dbAccess = $dbAccess;
        $this->fkValidator = $fkValidator;
    }

    public static function create(ContainerInterface $container) {

        $dbAccess = $container->get('db_access');
        $fkValidator = $container->get('fk_validator');

        return new static($dbAccess, $fkValidator );
    }

    public function getFormId() {
        return 'customers_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state) {

        $form ['data_selector'] = [
            '#type' => 'select',
            '#title' => $this->t('Type of entry'),
            '#options' => [
                '1' => $this->t('List or Delete Customers'),
                '2' => $this->t('Add Customers'),
                '3' => $this->t('Edit Customers'),
            ],
        ];


        //3. To list existing customers
                //3.1 start with table headers
                $customerHeaders = [
                    'id' => $this->t('ID'),
                    'fn' => $this->t('First Name'),
                    'ln' => $this->t('LastName'),
                    'em' => $this->t('Email'),
                    'phone' => $this->t('Phone'),
                ];
                //3.2 Call DB access service to return a customer array
                $allCustomers = $this->dbAccess->getCustomers();
                //2.3 use customer array and headers to build render array as table on a form

                $form['list'] = [
                    '#type' => 'tableselect',
                    '#caption' => $this
                        ->t('All Customers'),
                    '#header' => $customerHeaders,
                    '#options' => $allCustomers,
                ];



        //To add new customer
        $form ['customer']['client_id'] = [
            '#type' => 'textfield',
            '#size' => 20,
            '#title' => $this->t('Client ID'),
        ];

        $form ['customer']['first_name'] = [
            '#type' => 'textfield',
            '#size' => 20,
            '#title' => $this->t('First Name'),
        ];

        $form ['customer']['last_name'] = [
            '#type' => 'textfield',
            '#size' => 20,
            '#title' => $this->t('Last Name'),
        ];

        $form ['customer']['email'] = [
            '#type' => 'textfield',
            '#size' => 20,
            '#title' => $this->t('email'),
        ];

        $form ['customer']['phone'] = [
            '#type' => 'textfield',
            '#size' => 20,
            '#title' => $this->t('phone number'),
        ];

        $form ['customer']['add']= [
            '#type' => 'submit',
            '#value' => $this->t('Add this!'),
        ];

        $form['#cache']['max-age'] = 0;
        return $form;
    }

    public function submitForm(array &$form, FormStateInterface $form_state){
        $clientData = [
            'client_id' => $form_state->getValue('client_id'),
            'first_name' => $form_state->getValue('first_name'),
            'last_name' => $form_state->getValue('last_name'),
            'email' => $form_state->getValue('email'),
            'phone' => $form_state->getValue('phone'),
        ];

        $targetTable = 'customer';
        $this->dbAccess->insertNewRecord($clientData, $targetTable);

    }

    public function validateForm(array &$form, FormStateInterface $form_state){

    }







}
