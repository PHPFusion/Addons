function display_cr(startr,cd_idr,mn,link){
    var lnk = link;
    var end = 0; // change this to stop the counter at a higher value
    var refresh=1000; // Refresh rate in milli seconds

	    if((startr <= 0) && (end == 0)){
  		 window.location = lnk  // redirects to specified page once timer ends and ok button is pressed
	    } else {
        mytime=setTimeout('display_ctr('+startr+',"'+cd_idr+'",'+mn+',"'+lnk+'")',refresh)

	    }

}

function display_ctr(startr,cd_idr,mn,link) {
	var lnk = link;
    // Calculate the number of days left
    var days=Math.floor(startr / 86400);
    // After deducting the days calculate the number of hours left
    var hours = Math.floor((startr - (days * 86400 ))/3600);
    if (hours<10) { hours = '0' + hours;}
    // After days and hours , how many minutes are left
    var minutes = Math.floor((startr - (days * 86400 ) - (hours *3600 ))/60);
    if (minutes<10) { minutes = '0' + minutes;}
    // Finally how many seconds left after removing days, hours and minutes.
    var secs = Math.floor((startr - (days * 86400 ) - (hours *3600 ) - (minutes*60)));
    if (secs<10) { secs = '0' + secs;}

    var xx = minutes + ' : ' + secs;
    if (mn === 1) {
    xx = days + ' . ' + hours + ' : ' + minutes + ' : ' + secs;

    } else if (mn === 0) {
    xx = hours + ' : ' + minutes + ' : ' + secs;

    }

    document.getElementById(cd_idr).innerHTML = xx;
    startr= startr- 1;
    ttr=display_cr(startr,cd_idr,mn,lnk);
}

