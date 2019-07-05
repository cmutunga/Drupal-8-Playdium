<?php

namespace Drupal\cars_showroom\Form;

use drupal\cars_showroom\DBInterface\DataAccess;
use drupal\cars_showroom\DBInterface\FKValidator;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CarsForm extends FormBase{

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
        return 'cars_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state) {


                $possibleViews = [
                    '1' => $this->t('List or Delete Cars'),
                    '2' => $this->t('Add Cars'),
                    '3' => $this->t('Edit Cars'),
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

                //1. Build & return list of cars
                // 1.1 start with table headers
                $carHeaders = [
                    'id' => $this->t('ID'),
                    'mk' => $this->t('Make'),
                    'md' => $this->t('Model'),
                    'cl' => $this->t('Color'),
                    'od' => $this->t('Odo'),
                    'yr' => $this->t('Year'),
                    'lp' => $this->t('List'),
                    'emp' => $this->t('EmpID'),
                ];

                //1.2 Call DB access service to return an inventory array
                $carInventory = $this->dbAccess->getInventory();
                //1.3 use inventory array and headers to build render array as table on a form

        //xxx
                $form['form_container'] = [
                    '#type' => 'container',
                    // Note that the ID here matches with the 'wrapper' value use for the
                    // instrument family field's #ajax property.
                    '#attributes' => ['id' => 'form-container'],
                ];


                $form ['form_container']['list_cars_container'] = [
                    '#type' => 'container',
                    // Note that the ID here matches with the 'wrapper' value use for the
                     // instrument family field's #ajax property.
                    '#attributes' => ['id' => 'list-cars-container'],
                ];

                $form ['form_container']['list_cars_container']['list_fieldset'] = [
                    '#type' => 'fieldset',
                    '#title' => $this->t('List of cars, delete selected'),
                ];

                $form ['form_container']['list_cars_container']['list_fieldset']['list']  = [
                    '#type' => 'tableselect',
                    '#caption' => $this ->t('Cars For Sale'),
                    '#header' => $carHeaders,
                    '#multiple' => FALSE,
                    '#options' => $carInventory,
                ];

                $form ['form_container']['list_cars_container']['list_fieldset']['action_delete']= [
                    '#type' => 'submit',
                    '#value' => $this->t('Update'),
                ];

                $form ['form_container']['edit_cars_container'] = [
                    '#type' => 'container',
                    // Note that the ID here matches with the 'wrapper' value use for the
                     // instrument family field's #ajax property.
                    '#attributes' => ['id' => 'edit-cars-container'],
                ];

                $form ['form_container']['edit_cars_container']['list_fieldset'] = [
                    '#type' => 'fieldset',
                    '#title' => $this->t('List of cars, edit selected'),
                ];

                $form ['form_container']['edit_cars_container']['list_fieldset']['edit']  = [
                    '#type' => 'tableselect',
                    '#caption' => $this ->t('Cars For Sale'),
                    '#header' => $carHeaders,
                    '#multiple' => FALSE,
                    '#options' => $carInventory,
                ];

                $form ['form_container']['edit_cars_container']['list_fieldset']['action_edit']= [
                    '#type' => 'submit',
                    '#value' => $this->t('Update'),
                ];

                $form ['form_container']['add_cars_container'] = [
                    '#type' => 'container',
                    // Note that the ID here matches with the 'wrapper' value use for the
                    // instrument family field's #ajax property.
                    '#attributes' => ['id' => 'add-cars-container'],
                ];

                $form ['form_container']['add_cars_container']['list_fieldset'] = [
                    '#type' => 'fieldset',
                    '#title' => $this->t('List of cars, edit selected'),
                ];

                $form ['form_container']['add_cars_container']['list_fieldset']['car_id'] = [
                    '#type' => 'textfield',
                    '#size' => 20,
                    '#title' => $this->t('Car ID'),
                ];

                $form ['form_container']['add_cars_container']['list_fieldset']['make'] = [
                    '#type' => 'textfield',
                    '#size' => 20,
                    '#title' => $this->t('Car Make'),
                ];

                $form ['form_container']['add_cars_container']['list_fieldset']['model'] = [
                    '#type' => 'textfield',
                    '#size' => 20,
                    '#title' => $this->t('Model'),
                ];

                $form ['form_container']['add_cars_container']['list_fieldset']['color'] = [
                    '#type' => 'textfield',
                    '#size' => 20,
                    '#title' => $this->t('Color'),
                ];

                $form ['form_container']['add_cars_container']['list_fieldset']['odo'] = [
                    '#type' => 'textfield',
                    '#size' => 20,
                    '#title' => $this->t('Odo Reading'),
                ];

                $form ['form_container']['add_cars_container']['list_fieldset']['year'] = [
                    '#type' => 'textfield',
                    '#size' => 20,
                    '#title' => $this->t('Year'),
                ];

                $form ['form_container']['add_cars_container']['list_fieldset']['list_price'] = [
                    '#type' => 'textfield',
                    '#size' => 20,
                    '#title' => $this->t('List Price'),
                ];

                //FK validation -- ensures only bonafide sales persons can be entered in this field
                $salesPersons = $this->fkValidator->getSalesPersons();
                $form ['form_container']['add_cars_container']['list_fieldset']['emp_id'] = [
                    '#type' => 'select',
                    '#title' => $this->t('Sales Person'),
                    '#options' => $salesPersons,
                ];

                $form ['form_container']['add_cars_container']['list_fieldset']['action_add']= [
                    '#type' => 'submit',
                    '#value' => $this->t('Update'),
                ];


        // $selectedFrmView = $form_state->getValue('action_dropdown');

                switch ($selectedFrmView) {
                    case '1':
                        $form['form_container']['add_cars_container'] = ['#access' => 'false'];
                        $form['form_container']['edit_cars_container'] = ['#access' => 'false'];
                        break;

                    case '2':
                        $form['form_container']['list_cars_container'] = ['#access' => 'false'];
                        $form['form_container']['edit_cars_container'] = ['#access' => 'false'];
                        break;

                    case '3':
                        $form['form_container']['add_cars_container'] = ['#access' => 'false'];
                        $form['form_container']['list_cars_container'] = ['#access' => 'false'];
                        break;
                }

                $form['#cache']['max-age'] = 0;
                return $form;
    }

    public function submitForm(array &$form, FormStateInterface $form_state){

        $trigger = (string) $form_state->getTriggeringElement()['#value'];

        if ($trigger == 'Update') {
            // Process submitted form data.

            // grab car element values and build them into array that represents a DB table record
            $carData = [
                'car_id' => $form_state->getValue('car_id'),
                'make' => $form_state->getValue('make'),
                'model' => $form_state->getValue('model'),
                'color' => $form_state->getValue('color'),
                'odo' => $form_state->getValue('odo'),
                'year' => $form_state->getValue('year'),
                'list_price' => $form_state->getValue('list_price'),
                'emp_id' => $form_state->getValue('emp_id'),
            ];
            //insert record into DB table
            //$this->connection->insert('car_inventory')->fields($carData)->execute();
            $targetTable = 'car_inventory';
            $this->dbAccess->insertNewRecord($carData, $targetTable);

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
