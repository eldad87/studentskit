<?php
App::import('Helper', 'Time');
/**
 * Helper that override TimeHelper, and use the default server timezone in order to print date.
 * This is usefully when the dates are already converted to user-timezone using TimeBehavior in models
 */
class TimeTZHelper extends TimeHelper {

    public function nice($dateString = null, $format = null) {
        return $this->_engine->nice($dateString, date_default_timezone_get(), $format);
    }

    public function niceShort($dateString = null) {
        return $this->_engine->niceShort($dateString, date_default_timezone_get());
    }
}