<?php
// get bookings from cache
	$cacheKey = $hook->getValue('cachekey');
	$bookingList = $modx->cacheManager->get($cacheKey);

	if( !empty($bookingList) && count($bookingList) > 0 ){
		$bookingHtmlList = "";
		$bookingTotal = 0;	
		
		foreach( $bookingList as $bookingItem ){
			$bookingHtmlList .= "<h3 style=\"padding:0;margin-top:20px;margin-bottom:5px;\"><a href=\"[[++site_url]]".$bookingItem['url']."\">".$bookingItem['name']."<span style=\"font-size:12px\">(".$bookingItem['code'].")</span></a></h3>";
		
			$bookingHtmlList .= "<table>";
			$bookingHtmlList .= "<tr><th style=\"text-align:left;\">Rate Type</th><th style=\"text-align:left;\">Start Date</th><th style=\"text-align:left;\">Duration</th><th style=\"text-align:right;\">Price </th></tr>";
			
			$tRateItem = 0;
			foreach( $bookingItem['rates'] as $rateItem ){
				// Check id item amount (pax) can change
				if( empty($rateItem['flexpax'])  ){
					$itemType = $rateItem['typename'];
				}else{
					$itemType = $rateItem['pax'] . " x "  . $rateItem['typename'];
				}

				// Set Item HTML
				$bookingHtmlList .= "<tr>";
				$bookingHtmlList .= "<td align=\"left\">" . $itemType ."</td>";
				$bookingHtmlList .= "<td align=\"left\">" . date("d M Y",$rateItem['tripdatestart']) . "</td>";
				$bookingHtmlList .= "<td align=\"left\">" . $rateItem['duration'] . "</td>";
				$bookingHtmlList .= "<td align=\"right\">" . number_format($rateItem['priceTotal'],0) . " " . $rateItem['currency'] . "</td>";
				$bookingHtmlList .= "</tr>";
			
				$bookingTotal += $rateItem['priceTotal'];
			}

			$bookingHtmlList .= "<tr style=\"margin-top:20px;font-size:14px;font-weight:bold;\"><td align=\"left\" colspan=\"3\">Total:</td><td align=\"right\">" . number_format($bookingTotal,0) . " " . $rateItem['currency'] . "</td>";

			$bookingHtmlList .= "</table>";
		}
		$hook->setValue('bookinginfo',$bookingHtmlList);
		return true;	
	}else{
		$errorMsg = 'Invalid Booking Information:' . $cacheKey;
		$hook->addError('user',$errorMsg);
		return false;
	}