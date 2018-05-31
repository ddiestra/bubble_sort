<?php
/**
 * @file
 * Contains \Drupal\bubble_sort\Form\BubbleSortForm.
 */

namespace Drupal\bubble_sort\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use \Drupal\Core\Cache\Cache;

class BubbleSortForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'bubble_sort_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Form constructor.
    $form = parent::buildForm($form, $form_state);
    // Default settings.
    $config = $this->config('bubble_sort.settings');

    // Page title field.
    $form['array_length'] = array(
      '#type' => 'number',
      '#title' => $this->t('Length of the array:'),
      '#default_value' => $config->get('bubble_sort.array_length'),
      '#description' => $this->t('Define the size of numbers to be sort. Max value 30'),
    );

    // Source text field.
    $form['max_integer'] = array(
      '#type' => 'number',
      '#title' => $this->t('Max integer value:'),
      '#default_value' => $config->get('bubble_sort.max_integer'),
      '#description' => $this->t('The max value of the integers, it should always be higher than the array length and have a max value of 500.'),
    );

    // Source text field.
    $form['default_color'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Default color of rows:'),
      '#default_value' => $config->get('bubble_sort.default_color'),
      '#description' => $this->t('It should be the color expressed in hexadecimal.'),
    );

    // Source text field.
    $form['highlighted_color'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('The highlighted color of rows:'),
      '#default_value' => $config->get('bubble_sort.highlighted_color'),
      '#description' => $this->t('It should be the color expressed in hexadecimal.'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    $values = $form_state->getValues();

    if ($values['max_integer'] < $values['array_length']) {
      $form_state->setErrorByName('max_integer', $this->t('This value should always be higher than the array length.'));
    }

    if($values['array_length'] > 30){
      $form_state->setErrorByName('array_length', $this->t('To avoid complications the max value is 30.'));
    }

    if($values['max_integer'] > 500){
      $form_state->setErrorByName('max_integer', $this->t('To avoid complications the max value is 500.'));
    }

    if (substr($values['default_color'], 0, 1) !== "#" || !in_array(strlen($values['default_color']),[4,7])){
      $form_state->setErrorByName('default_color', $this->t('This value is not a valid hexadecimal.'));
    }

    if (substr($values['highlighted_color'], 0, 1) !== "#" || !in_array(strlen($values['highlighted_color']),[4,7])){
      $form_state->setErrorByName('highlighted_color', $this->t('This value is not a valid hexadecimal.'));
    }


  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('bubble_sort.settings');
    $config->set('bubble_sort.array_length', $form_state->getValue('array_length'));
    $config->set('bubble_sort.max_integer', $form_state->getValue('max_integer'));
    $config->set('bubble_sort.default_color', $form_state->getValue('default_color'));
    $config->set('bubble_sort.highlighted_color', $form_state->getValue('highlighted_color'));
    $config->save();

    Cache::invalidateTags(['bubble-sort-view']);        


    return parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'bubble_sort.settings',
    ];
  }

}