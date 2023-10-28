<?php
namespace Drupal\custom_form\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class CustomFormConfigForm extends ConfigFormBase {
  protected function getEditableConfigNames() {
    return ['custom_form.settings'];
  }

  public function getFormId() {
    return 'custom_form_config_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('custom_form.custom_form_settings');

    $form['heading'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Heading'),
      '#default_value' => $config->get('heading'),
      '#required' => TRUE,
    ];

    $form['date'] = [
      '#type' => 'date',
      '#title' => $this->t('Date'),
      '#default_value' => $config->get('date'),
      '#required' => TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('custom_form.settings')
      ->set('heading', $form_state->getValue('heading'))
      ->set('date', $form_state->getValue('date'))
      ->save();

    parent::submitForm($form, $form_state);
    $form_state->setRedirect('custom_form.custom_form');
  }
}
