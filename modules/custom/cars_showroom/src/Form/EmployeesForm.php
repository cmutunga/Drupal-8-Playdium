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

        $form ['action_selector'] = [
            '#type' => 'select',
            '#title' => $this->t('Type of entry'),
            '#options' => [
                '1' => $this->t('List or Delete Employees'),
                '2' => $this->t('Add Employees'),
                '3' => $this->t('Edit Employees'),
            ],
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

        $form['list_employees_container'] = [
            '#type' => 'container',
            // Note that the ID here matches with the 'wrapper' value use for the
            // instrument family field's #ajax property.
            '#attributes' => ['id' => 'list_employees_container'],
        ];

        $form['list_employees_container']['list_fieldset'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('List of employees, delete selected'),
        ];



                $form ['list_employees_container']['list_fieldset']['list'] = [
                    '#type' => 'tableselect',
                    '#caption' => $this ->t('All Employees'),
                    '#header' => $employeeHeaders,
                    '#multiple' => FALSE,
                    '#options' => $allEmployees,
                ];

                $form ['list_employees_container']['list_fieldset']['action_delete']= [
                    '#type' => 'submit',
                    '#value' => $this->t('Delete Selected!'),
                ];

                $form['edit_employees_container'] = [
                    '#type' => 'container',
                    // Note that the ID here matches with the 'wrapper' value use for the
                    // instrument family field's #ajax property.
                    '#attributes' => ['id' => 'edit_employees_container'],
                 ];

                $form['edit_employees_container']['list_fieldset'] = [
                    '#type' => 'fieldset',
                    '#title' => $this->t('List of employees, edit selected'),
                ];

                $form ['edit_employees_container']['list_fieldset']['edit'] = [
                    '#type' => 'tableselect',
                    '#caption' => $this ->t('All Employees'),
                    '#header' => $employeeHeaders,
                    '#multiple' => FALSE,
                    '#options' => $allEmployees,
                ];

                $form ['edit_employees_container']['list_fieldset']['action_edit']= [
                    '#type' => 'submit',
                    '#value' => $this->t('Edit Selected!'),
                ];

                $form['add_employees_container'] = [
                    '#type' => 'container',
                    // Note that the ID here matches with the 'wrapper' value use for the
                    // instrument family field's #ajax property.
                    '#attributes' => ['id' => 'add_employees_container'],
                ];

                $form['add_employees_container']['list_fieldset'] = [
                    '#type' => 'fieldset',
                    '#title' => $this->t('Enter employee attributes and press button'),
                ];


                $form ['add_employees_container']['list_fieldset']['emp_id'] = [
                    '#type' => 'textfield',
                    '#size' => 20,
                    '#title' => $this->t('Employee ID'),
                ];

                $form ['add_employees_container']['list_fieldset']['first_name'] = [
                    '#type' => 'textfield',
                    '#size' => 20,
                    '#title' => $this->t('First Name'),
                ];

                $form ['add_employees_container']['list_fieldset']['last_name'] = [
                    '#type' => 'textfield',
                    '#size' => 20,
                    '#title' => $this->t('Last Name'),
                ];

                $form ['add_employees_container']['list_fieldset']['email'] = [
                    '#type' => 'textfield',
                    '#size' => 20,
                    '#title' => $this->t('email'),
                ];

                $form ['add_employees_container']['list_fieldset']['is_mnr'] = [
                    '#type' => 'select',
                    '#title' => $this->t('Is Manager?'),
                    '#options' => [
                        '0' => $this->t('No'),
                        '1' => $this->t('Yes'),
                    ],
                ];

                //FK validation --- ensure only bonafide managers can be entered in this field
                $managers = $this->fkValidator->getManagers();

                $form ['add_employees_container']['list_fieldset']['mnr_id'] = [
                    '#type' => 'select',
                    '#title' => $this->t('Manager'),
                    '#options' => $managers,
                ];

                $form ['add_employees_container']['list_fieldset']['action_add']= [
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

    public function validateForm(array &$form, FormStateInterface $form_state){

    }


}
