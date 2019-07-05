<?php

namespace Drupal\cars_showroom\Form;

use drupal\cars_showroom\DBInterface\DataAccess;
use drupal\cars_showroom\DBInterface\FKValidator;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EmployeesForm extends FormBase {

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
        return 'employees_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state) {

        $possibleViews = [
            '1' => $this->t('List or Delete Employees'),
            '2' => $this->t('Add Employees'),
            '3' => $this->t('Edit Employees'),
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


                //2. Build list of employees
                //2.1 start with table headers
                $employeeHeaders = [
                    'id' => $this->t('ID'),
                    'fn' => $this->t('First Name'),
                    'ln' => $this->t('LastName'),
                    'em' => $this->t('Email'),
                    'isMnr' => $this->t('Is Manager?'),
                    'mnrID' => $this->t('Manager ID'),
                ];
                //2.2 Call DB access service to return an employee array
                $allEmployees = $this->dbAccess->getEmployees();
                //2.3 use employee array and headers to build render array as table on a form

        //xxx
        $form['form_container'] = [
            '#type' => 'container',
            // Note that the ID here matches with the 'wrapper' value use for the
            // instrument family field's #ajax property.
            '#attributes' => ['id' => 'form-container'],
        ];




        $form ['form_container']['list_employees_container'] = [
            '#type' => 'container',
            // Note that the ID here matches with the 'wrapper' value use for the
            // instrument family field's #ajax property.
            '#attributes' => ['id' => 'list-employees-container'],
        ];

        $form ['form_container']['list_employees_container']['list_fieldset'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('List of employees, delete selected'),
        ];



                $form ['form_container']['list_employees_container']['list_fieldset']['list'] = [
                    '#type' => 'tableselect',
                    '#caption' => $this ->t('All Employees'),
                    '#header' => $employeeHeaders,
                    '#multiple' => FALSE,
                    '#options' => $allEmployees,
                ];

                $form ['form_container']['list_employees_container']['list_fieldset']['action_delete']= [
                    '#type' => 'submit',
                    '#value' => $this->t('Update'),
                ];

                $form ['form_container']['edit_employees_container'] = [
                    '#type' => 'container',
                    // Note that the ID here matches with the 'wrapper' value use for the
                    // instrument family field's #ajax property.
                    '#attributes' => ['id' => 'edit-employees-container'],
                 ];

                $form ['form_container']['edit_employees_container']['list_fieldset'] = [
                    '#type' => 'fieldset',
                    '#title' => $this->t('List of employees, edit selected'),
                ];

                $form ['form_container']['edit_employees_container']['list_fieldset']['edit'] = [
                    '#type' => 'tableselect',
                    '#caption' => $this ->t('All Employees'),
                    '#header' => $employeeHeaders,
                    '#multiple' => FALSE,
                    '#options' => $allEmployees,
                ];

                $form ['form_container']['edit_employees_container']['list_fieldset']['action_edit']= [
                    '#type' => 'submit',
                    '#value' => $this->t('Update'),
                ];

                $form ['form_container']['add_employees_container'] = [
                    '#type' => 'container',
                    // Note that the ID here matches with the 'wrapper' value use for the
                    // instrument family field's #ajax property.
                    '#attributes' => ['id' => 'add-employees-container'],
                ];

                $form ['form_container']['add_employees_container']['list_fieldset'] = [
                    '#type' => 'fieldset',
                    '#title' => $this->t('Enter employee attributes and press button'),
                ];


                $form ['form_container']['add_employees_container']['list_fieldset']['emp_id'] = [
                    '#type' => 'textfield',
                    '#size' => 20,
                    '#title' => $this->t('Employee ID'),
                ];

                $form ['form_container']['add_employees_container']['list_fieldset']['first_name'] = [
                    '#type' => 'textfield',
                    '#size' => 20,
                    '#title' => $this->t('First Name'),
                ];

                $form ['form_container']['add_employees_container']['list_fieldset']['last_name'] = [
                    '#type' => 'textfield',
                    '#size' => 20,
                    '#title' => $this->t('Last Name'),
                ];

                $form ['form_container']['add_employees_container']['list_fieldset']['email'] = [
                    '#type' => 'textfield',
                    '#size' => 20,
                    '#title' => $this->t('email'),
                ];

                $form ['form_container']['add_employees_container']['list_fieldset']['is_mnr'] = [
                    '#type' => 'select',
                    '#title' => $this->t('Is Manager?'),
                    '#options' => [
                        '0' => $this->t('No'),
                        '1' => $this->t('Yes'),
                    ],
                ];

                //FK validation --- ensure only bonafide managers can be entered in this field
                $managers = $this->fkValidator->getManagers();

                $form ['form_container']['add_employees_container']['list_fieldset']['mnr_id'] = [
                    '#type' => 'select',
                    '#title' => $this->t('Manager'),
                    '#options' => $managers,
                ];

                $form ['form_container']['add_employees_container']['list_fieldset']['action_add']= [
                    '#type' => 'submit',
                    '#value' => $this->t('Update'),
                 ];

        switch ($selectedFrmView) {
            case '1':
                $form['form_container']['add_employees_container'] = ['#access' => 'false'];
                $form['form_container']['edit_employees_container'] = ['#access' => 'false'];
                break;

            case '2':
                $form['form_container']['list_employees_container'] = ['#access' => 'false'];
                $form['form_container']['edit_employees_container'] = ['#access' => 'false'];

                break;

            case '3':
                $form['form_container']['add_employees_container'] = ['#access' => 'false'];
                $form['form_container']['list_employees_container'] = ['#access' => 'false'];
                break;
        }






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

        $trigger = (string) $form_state->getTriggeringElement()['#value'];

        if ($trigger == 'Update') {
            // Process submitted form data.

            //insert a row as specified on the form elements to the target table
            $empData = [
                'emp_id' => $form_state->getValue('emp_id'),
                'first_name' => $form_state->getValue('first_name'),
                'last_name' => $form_state->getValue('last_name'),
                'email' => $form_state->getValue('email'),
                'is_manager' => $form_state->getValue('is_mnr'),
                'manager_id' => $form_state->getValue('mnr_id'),
            ];
            //insert record into DB table
            $targetTable = 'employee';
            $this->dbAccess->insertNewRecord($empData, $targetTable);

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
