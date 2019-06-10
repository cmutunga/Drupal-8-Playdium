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

        /*
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
                $form['list'] = [
                    '#type' => 'tableselect',
                    '#caption' => $this
                        ->t('All Employees'),
                    '#header' => $employeeHeaders,
                    '#options' => $allEmployees,
                ];

          */
                $form ['add']['emp_id'] = [
                    '#type' => 'textfield',
                    '#size' => 20,
                    '#title' => $this->t('Employee ID'),
                ];

                $form ['add']['first_name'] = [
                    '#type' => 'textfield',
                    '#size' => 20,
                    '#title' => $this->t('First Name'),
                ];

                $form ['add']['last_name'] = [
                    '#type' => 'textfield',
                    '#size' => 20,
                    '#title' => $this->t('Last Name'),
                ];

                $form ['add']['email'] = [
                    '#type' => 'textfield',
                    '#size' => 20,
                    '#title' => $this->t('email'),
                ];

                $form ['add']['is_mnr'] = [
                    '#type' => 'select',
                    '#title' => $this->t('Is Manager?'),
                    '#options' => [
                        '0' => $this->t('No'),
                        '1' => $this->t('Yes'),
                    ],
                ];

                //FK validation --- ensure only bonafide managers can be entered in this field
                $managers = $this->fkValidator->getManagers();

                $form ['add']['mnr_id'] = [
                    '#type' => 'select',
                    '#title' => $this->t('Manager'),
                    '#options' => $managers,
                ];

                $form ['add']['add_employee']= [
                    '#type' => 'submit',
                    '#value' => $this->t('Add this!'),
                ];


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
