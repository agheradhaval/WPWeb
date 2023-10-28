<?php
namespace Drupal\custom_form\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\file\Entity\File;
use Drupal\Core\Ajax\HtmlCommand;

class CustomForm extends FormBase {
  public function getFormId() {
    return 'custom_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    
    $config = \Drupal::config('custom_form.settings');
    $heading = $config->get('heading');
    $date = $config->get('date');

    $form['heading'] = [
      '#markup' => '<div class="custome_form"><h3>' . $heading . '</h3>',
    ];

    $form['date'] = [
      '#markup' => '<h3>' . $date . '</h3></div>',
    ];

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#required' => TRUE,
    ];

    $form['runame'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Runame'),
      '#required' => TRUE,
    ];

    $form['dob'] = [
      '#type' => 'date',
      '#title' => $this->t('Date of Birth'),
      '#date_date_format' => 'Y-m-d', // Date format set to Year-Month-Day.
      '#required' => TRUE,
    ];

    $form['gender'] = [
      '#type' => 'select',
      '#title' => $this->t('Gender'),
      '#options' => ['male' => 'Male', 'female' => 'Female'],
      '#required' => TRUE,
    ];

    $form['photo'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Upload Image'),
      '#upload_location' => 'public://images/', // Specify the upload directory.
      '#upload_validators' => [
        'file_validate_extensions' => ['jpg jpeg png gif'],
      ],
    ];
    $form['#attached']['library'][] = 'custom_form/custom-form-styles';
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];



    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {

    $date_now = date("Y-m-d"); // this format is string comparable
    if ($date_now < $form_state->getValue('dob')) {
      $form_state->setErrorByName('dob', t('The dob field should less then Today.'));
    }
    
    $value = $form_state->getValue('name');
    if (!preg_match('/^[A-Za-z]+$/', $value)) {
      $form_state->setErrorByName('name', t('The name field should contain only letters.'));
    }
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    $file = $form_state->getValue('photo');
    if (!empty($file[0])) {
      $file_entity = \Drupal\file\Entity\File::load($file[0]);
      $file_entity->setPermanent();
      $file_entity->save();
      $file_id = $file_entity->id();
    }

    $connection = Database::getConnection();
    $connection->insert('custom_form_data')
      ->fields([
        'name' => $values['name'],
        'runame' => $values['runame'],
        'date_of_birth' => $values['dob'],
        'gender' => $values['gender'],
        'photo' => $file_id,
      ])
      ->execute();

    $form_state->setRedirect('custom_form.submitted_records');
  }
}
