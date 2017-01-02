<?php

/**
 * konzerte-ostschweiz.ch Concert API
 *
 * Copyright (c) konzerte-ostschweiz.ch
 *
 * @package    Konzerteostschweiz
 * @link       https://www.konzerte-ostschweiz.ch
 * @author     Marco Simbürger <develop@konzerte-ostschweiz.ch>
 *
 */

namespace Konzerteostschweiz;

use Contao\CalendarEventsModel;
use Contao\CoreBundle\Monolog\ContaoContext;
use Psr\Log\LogLevel;

/**
 * Provides methods to update concert events.
 */
class Concert {

  /**
   * Update concerts in Contao event calendar.
   */
  public function updateConcerts() {
      $data = $this->getData();
      if (!empty($data)) {
        $this->updateTlCalendarEvents($data->concerts);
      }
  }

  /**
   * Get the data from the JSON API.
   *
   * @return array|null
   *   Return the data as array or null.
   */
  private function getData() {
    $restUrl = $GLOBALS['TL_CONFIG']['ko_concertapi_rest_url'];
    if (!empty($restUrl)) {
      $curl = curl_init();
      curl_setopt($curl, CURLOPT_URL, $restUrl);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      $response = curl_exec($curl);
      curl_close($curl);

      return json_decode($response);
    }

    return NULL;
  }

  /**
   * Update Contao events (tl_calendar_events).
   *
   * @param array $arrData
   *   The concert data.
   */
  private function updateTlCalendarEvents(array $arrData) {
    foreach ($arrData as $data) {
      // konzerte-ostschweiz.ch concert id.
      $koConcertId = (int) $data->concert->id;
      if (!empty($koConcertId)) {
        if ($this->checkConcertId($koConcertId)) {
          if ($this->checkConcertChanges($koConcertId, $data->concert->last_updated)) {
            $this->updateConcert($koConcertId, $data);
          }
        }
        else {
          $this->addConcert($data);
        }
      }
    }
  }

  /**
   * Check if the concert already exists.
   *
   * @param int $koConcertId
   *   The concert id of konzerte-ostschweiz.ch.
   *
   * @return bool
   *   Return true or false.
   */
  private function checkConcertId($koConcertId) {
    $concert = $this->getConcertByKoId($koConcertId);
    if (!empty($concert)) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Check if the concert has changes.
   *
   * @param int $koConcertId
   *   The concert id of konzerte-ostschweiz.ch.
   * @param int $lastUpdate
   *   The timestamp of the last update.
   *
   * @return bool
   *   Return true or false.
   */
  private function checkConcertChanges($koConcertId, $lastUpdate) {
    $concert = $this->getConcertByKoId($koConcertId);
    if ($concert->ko_tstamp < $lastUpdate) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Add a new concert entry.
   *
   * @param $data
   *   The concert data.
   *
   * @return bool|int
   *    Return the Contao event id or false.
   */
  private function addConcert($data) {
      $newConcert = new \CalendarEventsModel();
      $concertDataArray = $this->getConcertDataArray($data);
      if (!empty($concertDataArray)) {
        $newConcert->setRow($concertDataArray);
        $newConcert->save();
        $this->generateAlias($newConcert);
        $id = $newConcert->id;
        $this->writeLogMessage('New concert (ID ' . $id . ') has been saved.', __FUNCTION__);

        return $id;
    }

    return FALSE;
  }

  /**
   * Update an existing concert entry.
   *
   * @param int $koConcertId
   *   The concert id of konzerte-ostschweiz.ch.
   * @param $data
   *   The concert data.
   *
   * @return bool|int
   *    Return the Contao event id or false.
   */
  private function updateConcert($koConcertId, $data) {
    $existingConcert = $this->getConcertByKoId($koConcertId);
    if (!empty($existingConcert)) {
      $concertDataArray = $this->getConcertDataArray($data);
      if (!empty($concertDataArray)) {
        $id = $existingConcert->id;
        \Database::getInstance()->prepare("UPDATE tl_calendar_events %s WHERE id=?")->set($concertDataArray)->execute($id);
        $existingConcert->refresh();
        $this->generateAlias($existingConcert);
        $this->writeLogMessage('Concert (ID ' . $id . ') has been updated.', __FUNCTION__);

        return $id;
      }
    }

    return FALSE;
  }

  /**
   * Get the Contao event model by event id.
   *
   * @param int $calendarId
   *   The Contao calendar id.
   *
   * @return \Contao\CalendarModel|null
   *   Return the Contao calendar model or null.
   */
  private function getCalendarById($calendarId) {
    return \CalendarModel::findByPk($calendarId);
  }

  /**
   * Get the Contao event model by konzerte-ostschweiz.ch concert id.
   *
   * @param int $koConcertId
   *   The concert id of konzerte-ostschweiz.ch.
   *
   * @return CalendarEventsModel|null
   *   Return the Contao event model or null.
   */
  private function getConcertByKoId($koConcertId) {
    return \CalendarEventsModel::findOneBy('koid', $koConcertId);
  }

  /**
   * Get the concert data array to insert or update the event entry.
   *
   * @param $data
   *   The concert data.
   * @return array|bool
   *   Return the concert data array or false.
   */
  private function getConcertDataArray($data) {
    $calendarId = $GLOBALS['TL_CONFIG']['concert_calendar'];
    if ($this->getCalendarById($calendarId)) {
      $concertDataArray = array(
        'pid' => $calendarId,
        'tstamp' => time(),
        'title' => $data->concert->title,
        'startDate' => $data->concert->start_date,
        'endDate' => $data->concert->end_date,
        'addTime' => 1,
        'startTime' => $data->concert->start_time,
        'endTime' => $data->concert->end_time,
        'location' => $data->concert->location,
        'teaser' => $data->concert->short_description,
        'source' => 'external',
        'url' => $data->concert->url,
        'target' => 1,
        'published' => 1,
        'koid' => $data->concert->id,
        'ko_tstamp' => $data->concert->last_updated,
        'association_name' => $data->association->association_name,
      );

      return $concertDataArray;
    }
    else {
      $this->writeLogMessage('Calendar with id "' . $calendarId . '" not found.', __FUNCTION__);
      return FALSE;
    }
  }

  /**
   * Generate alias for the Contao event.
   *
   * @param CalendarEventsModel $model
   *   Return the Contao event model.
   */
  private function generateAlias(CalendarEventsModel $model) {
    $alias = \StringUtil::generateAlias($model->title);
    $concerts = \CalendarEventsModel::findAll(array('alias' => $alias));

    if ($concerts->count() > 1) {
      $alias .= '-' . $model->id;
    }

    \Database::getInstance()->prepare('UPDATE tl_calendar_events SET alias = ? WHERE id = ?')->execute($alias, $model->id);
  }

  /**
   * Write log message.
   *
   * @param string $message
   *   The log message.
   *
   * @param string $function
   *   Return the function name.
   */
  private function writeLogMessage($message, $function) {
    $version = substr(VERSION, 0, 1);
    if ($version == 3) {
      \System::log($message, __CLASS__.'::'.$function, TL_CONFIGURATION);
    }
    elseif ($version == 4) {
      \System::getContainer()
        ->get('monolog.logger.contao')
        ->log(LogLevel::INFO, $message, array('contao' => new ContaoContext(__CLASS__ . '::' . $function, TL_CONFIGURATION)));
    }
  }
}
