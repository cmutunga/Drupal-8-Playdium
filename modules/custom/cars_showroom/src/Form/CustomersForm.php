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

        $possibleViews = [
            '1' => $this->t('List or Delete Customers'),
            '2' => $this->t('Add Customer'),
            '3' => $this->t('Edit Customer'),
        ];

        // Use a value, the first time the form loads. The key function
        // returns the index element of the current array position
        if (empty($form_state->getValue('action_dropdown'))) {

            $selectedFrmView = key($possibleViews);
        } else {
            // Get the value if it already exists.
            $selectedFrmView = $form_state->getValue('action_dropdown');
        }

        $form ['action_dropdown'] = [
            '#type' => 'select',
            '#title' => $this->t('Type of entry'),
            '#options' => $possibleViews,
            '#ajax' => [
                'callback' => '::formViewCallback',
                'event' => 'change',
                'wrapper' => 'form-container',
                //if not empty first option else 2nd option after ?
                '#default_value' =>   '',
            ]
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


                //xxx
                $form['form_container'] = [
                    '#type' => 'container',
                        // Note that the ID here matches with the 'wrapper' value use for the
                        // instrument family field's #ajax property.
                    '#attributes' => ['id' => 'form-container'],
                 ];


                $form['form_container']['list_customers_container'] = [
                    '#type' => 'container',
                    // Note that the ID here matches with the 'wrapper' value use for the
                    // instrument family field's #ajax property.
                    '#attributes' => ['id' => 'list-customers-container'],
                ];

                $form['form_container']['list_customers_container']['list_fieldset'] = [
                    '#type' => 'fieldset',
                    '#title' => $this->t('List of customers, delete selected'),
                ];


                $form ['form_container']['list_customers_container']['list_fieldset']['list'] = [
                    '#type' => 'tableselect',
                    '#caption' => $this->t('All Customers'),
                    '#header' => $customerHeaders,
                    '#multiple' => FALSE,
                    '#options' => $allCustomers,
                ];

                $form ['form_container']['list_customers_container']['list_fieldset']['action_delete']= [
                    '#type' => 'submit',
                    '#value' => $this->t('Update'),
                ];

                //xxx

                $form['form_container']['edit_customers_container'] = [
                    '#type' => 'container',
                    // Note that the ID here matches with the 'wrapper' value use for the
                    // instrument family field's #ajax property.
                    '#attributes' => ['id' => 'edit-customers-container'],
                ];

                $form['form_container']['edit_customers_container']['edit_fieldset'] = [
                    '#type' => 'fieldset',
                    '#title' => $this->t('List of customers, edit selected'),
                ];

                $form ['form_container']['edit_customers_container']['edit_fieldset']['edit'] = [
                    '#type' => 'tableselect',
                    '#caption' => $this ->t('All Customers'),
                    '#header' => $customerHeaders,
                    '#multiple' => FALSE,
                    '#options' => $allCustomers,
                ];

                $form ['form_container']['edit_customers_container']['edit_fieldset']['action_edit']= [
                    '#type' => 'submit',
                    '#value' => $this->t('Update'),
                ];

       //xxx

        $form['form_container']['add_customers_container'] = [
            '#type' => 'container',
            // Note that the ID here matches with the 'wrapper' value use for the
            // instrument family field's #ajax property.
            '#attributes' => ['id' => 'add-customers-container'],
        ];

        $form['form_container']['add_customers_container']['add_fieldset'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('Enter customer attributes and press button'),
        ];

        //To add new customer
        $form ['form_container']['add_customers_container']['add_fieldset']['client_id'] = [
            '#type' => 'textfield',
            '#size' => 20,
            '#title' => $this->t('Client ID'),
        ];

        $form ['form_container']['add_customers_container']['add_fieldset']['first_name'] = [
            '#type' => 'textfield',
            '#size' => 20,
            '#title' => $this->t('First Name'),
        ];

        $form ['form_container']['add_customers_container']['add_fieldset']['last_name'] = [
            '#type' => 'textfield',
            '#size' => 20,
            '#title' => $this->t('Last Name'),
        ];

        $form ['form_container']['add_customers_container']['add_fieldset']['email'] = [
            '#type' => 'textfield',
            '#size' => 20,
            '#title' => $this->t('email'),
        ];

        $form ['form_container']['add_customers_container']['add_fieldset']['phone'] = [
            '#type' => 'textfield',
            '#size' => 20,
            '#title' => $this->t('phone number'),
        ];

        $form ['form_container']['add_customers_container']['add_fieldset']['action_add']= [
            '#type' => 'submit',
            '#value' => $this->t('Update'),
        ];



       // $selectedFrmView = $form_state->getValue('action_dropdown');

        switch ($selectedFrmView) {
            case '1':
                $form['form_container']['add_customers_container'] = ['#access' => 'false'];
                $form['form_container']['edit_customers_container'] = ['#access' => 'false'];
                break;

            case '2':
                $form['form_container']['list_customers_container'] = ['#access' => 'false'];
                $form['form_container']['edit_customers_container'] = ['#access' => 'false'];

                break;

            case '3':
                $form['form_container']['add_customers_container'] = ['#access' => 'false'];
                $form['form_container']['list_customers_container'] = ['#access' => 'false'];
                break;
        }

        $form['#cache']['max-age'] = 0;
        return $form;
    }

    public function submitForm(array &$form, FormStateInterface $form_state){

        $trigger = (string) $form_state->getTriggeringElement()['#value'];

        if ($trigger == 'Update') {
            // Process submitted form data.

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
        else {
            // Rebuild the form. This causes buildForm() to be called again before the
            // associated Ajax callback. Allowing the logic in buildForm() to execute
            // and update the $form array so that it reflects the current state of
            // the instrument family select list.
            $form_state->setRebuild();
        }

    }

    public function formViewCallback(array $form, FormStateInterface $form_state) {
                return  $form['form_container'];
    }

    public function validateForm(array &$form, FormStateInterface $form_state){

    }

}
