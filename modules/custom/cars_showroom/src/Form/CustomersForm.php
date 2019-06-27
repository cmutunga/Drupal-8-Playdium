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

        $form ['action_selector'] = [
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

                $form['list_customers_container'] = [
                    '#type' => 'container',
            // Note that the ID here matches with the 'wrapper' value use for the
            // instrument family field's #ajax property.
                    '#attributes' => ['id' => 'list_customers_container'],
                ];

                $form['list_customers_container']['list_fieldset'] = [
                    '#type' => 'fieldset',
                    '#title' => $this->t('List of customers, delete selected'),
                ];


                $form ['list_customers_container']['list_fieldset']['list'] = [
                    '#type' => 'tableselect',
                    '#caption' => $this->t('All Customers'),
                    '#header' => $customerHeaders,
                    '#multiple' => FALSE,
                    '#options' => $allCustomers,
                ];

                $form ['list_customers_container']['list_fieldset']['action_delete']= [
                    '#type' => 'submit',
                    '#value' => $this->t('Delete Selected!'),
                ];

                $form['edit_customers_container'] = [
                    '#type' => 'container',
            // Note that the ID here matches with the 'wrapper' value use for the
            // instrument family field's #ajax property.
                    '#attributes' => ['id' => 'edit_customers_container'],
                ];

                $form['edit_customers_container']['edit_fieldset'] = [
                    '#type' => 'fieldset',
                    '#title' => $this->t('List of customers, edit selected'),
                ];

                $form ['edit_customers_container']['edit_fieldset']['edit'] = [
                    '#type' => 'tableselect',
                    '#caption' => $this ->t('All Customers'),
                    '#header' => $customerHeaders,
                    '#multiple' => FALSE,
                    '#options' => $allCustomers,
                ];

                $form ['edit_customers_container']['edit_fieldset']['action_edit']= [
                    '#type' => 'submit',
                    '#value' => $this->t('Edit Selected!'),
                ];

        $form['add_customers_container'] = [
            '#type' => 'container',
            // Note that the ID here matches with the 'wrapper' value use for the
            // instrument family field's #ajax property.
            '#attributes' => ['id' => 'add_customers_container'],
        ];

        $form['add_customers_container']['add_fieldset'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('Enter customer attributes and press button'),
        ];

        //To add new customer
        $form ['add_customers_container']['add_fieldset']['client_id'] = [
            '#type' => 'textfield',
            '#size' => 20,
            '#title' => $this->t('Client ID'),
        ];

        $form ['add_customers_container']['add_fieldset']['first_name'] = [
            '#type' => 'textfield',
            '#size' => 20,
            '#title' => $this->t('First Name'),
        ];

        $form ['add_customers_container']['add_fieldset']['last_name'] = [
            '#type' => 'textfield',
            '#size' => 20,
            '#title' => $this->t('Last Name'),
        ];

        $form ['add_customers_container']['add_fieldset']['email'] = [
            '#type' => 'textfield',
            '#size' => 20,
            '#title' => $this->t('email'),
        ];

        $form ['add_customers_container']['add_fieldset']['phone'] = [
            '#type' => 'textfield',
            '#size' => 20,
            '#title' => $this->t('phone number'),
        ];

        $form ['add_customers_container']['add_fieldset']['action_add']= [
            '#type' => 'submit',
            '#value' => $this->t('Add this!'),
        ];

        /*$form['add'] = ['#access' => 'false'];*/
        /*$form['list'] = ['#access' => 'false'];*/
        /*$form['edit'] = ['#access' => 'false'];*/
        /*$form['action_add'] = ['#access' => 'false'];*/
        /*$form['action_delete']= ['#access' =>  'false'];*/
        /*$form['action_edit']= ['#access' =>  'false'];*/

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
