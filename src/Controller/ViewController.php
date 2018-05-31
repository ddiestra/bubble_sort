<?php

namespace Drupal\bubble_sort\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal;

/**
 * ViewController.
 */
class ViewController extends ControllerBase {

  /**
   * Generates the view for the bubble sort animation.
   */
  public function view() {

    $config = $this->config('bubble_sort.settings');

    return [
      '#theme' => 'bubble_sort',
      '#color' => $config->get('bubble_sort.default_color'),
      '#hcolor' => $config->get('bubble_sort.highlighted_color'),
      '#len' => $config->get('bubble_sort.array_length'),
      '#attached' => [
        'library' => ['bubble_sort/bubble-sort-animation'],
      ],
      '#cache' => [
        'tags' => [
          'bubble-sort-view',
        ],
      ],
    ];
  }

  /**
   * Generates N random numbers with a max value of M.
   */
  public function shuffle() {

    $config = $this->config('bubble_sort.settings');
    $numbers = [];
    $max = $config->get('bubble_sort.max_integer');
    $size = $config->get('bubble_sort.array_length');

    while (count($numbers) < $size) {
      $nNumber = rand(0, $max);
      if (!in_array($nNumber, $numbers)) {
        $numbers[] = $nNumber;
      }
    }

    return new JsonResponse(['data' => $numbers]);
  }

  /**
   * Implementes the next step of the bubble sort.
   */
  public function step() {

    $current = Drupal::request()->request->get('current');
    $values = Drupal::request()->request->get('values');

    $response = [
      'current' => $current,
      'values' => $values,
      'completed' => FALSE,
    ];

    $response['swap'] = ($values[0] < $values[1]);
    $response['current']['nswap'] += (int) $response['swap'];

    $current['index']++;

    if ($current['index'] == ($current['limit'] - 1)) {
      $response['current']['index'] = 0;
      $response['current']['limit']--;

      if ($response['current']['limit'] == 1 || $response['current']['nswap'] == 0) {
        $response['completed'] = TRUE;
      }

      $response['current']['nswap'] = 0;
    }
    else {
      $response['current']['index'] = $current['index'];
    }

    return new JsonResponse(['data' => $response]);
  }

}
