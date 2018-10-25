<p>
[[+shortdescription]]
</p>
<div class="row clearfix">
  <div class="col-md-6 column">
      <img src="[[+imgthumb]]" title="[[+name]]" />
  </div>
  <div class="col-md-6 column">
      <table id="trip-details" class="table table-bordered">
				<thead>
					<tr>						
						<th colspan="2">
							[[%trip.details? &namespace=`sead` ]]
						</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td width="40%">
							[[%trip.code? &namespace=`sead` ]]:
						</td>
						<td class="trip-details-content">
							[[+code1]]
						</td>
					</tr>
					<tr>
						<td>
							[[%trip.destination? &namespace=`sead` ]]:
						</td>
						<td class="trip-details-content">
							[[+destinationname]]
						</td>
					</tr>
					<!--tr>
						<td>
							[[%trip.language? &namespace=`sead` ]]:
						</td>
						<td class="trip-details-content">
							[[+languagename]]
						</td>
					</tr-->
					<tr>
						<td>
							[[%trip.duration? &namespace=`sead` ]]:
						</td>
						<td class="trip-details-content">
							[[+durationname]]
						</td>
					</tr>
                                        <tr>
						<td>
							[[%trip.frequency? &namespace=`sead` ]]:
						</td>
						<td class="trip-details-content">
							[[+frequencynames]]
						</td>
					</tr>
                                        <tr>
						<td>
							[[%trip.departure.time? &namespace=`sead` ]]:
						</td>
						<td class="trip-details-content">
							[[+departure]]
						</td>
					</tr>
                                        <tr>
						<td>
							[[%trip.suitable.for? &namespace=`sead` ]]:
						</td>
						<td class="trip-details-content">
							[[+segmentnames]]
						</td>
					</tr>
                                        <tr>
						<td>
							[[%trip.theme? &namespace=`sead` ]]:
						</td>
						<td class="trip-details-content">
							[[+themenames]]
						</td>
					</tr>                                        

				</tbody>
			</table>
  </div>
</div>