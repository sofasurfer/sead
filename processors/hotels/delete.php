<?php
/**
 * Delete Hotel
 *
 * @param integer $id The ID of the hotel
 *
 * @package modx
 * @subpackage processors.hotel
 */

$_DATA = $scriptProperties;

if (empty($_DATA['id'])) return $modx->error->failure("Error no Hotel ID");
$hotel = $modx->getObject('seadHotel',$_DATA['id']);
if ($hotel == null) return $modx->error->failure("Error no Hotel found");

if ( $hotel->remove() == false ) {
    return $modx->error->failure("Error while deleting hotel");
}

return $modx->error->success('',$hotel);
