(function() {
	if(typeof(_igc) != "undefined")
	{
		var channel_id = _igc[0];
		var viewType = _igc[1];

		var e = document.createElement('div'); 
		e.id = '_igc_iframe_container';

		if(viewType == 'f')
		{
			e.style.position 	= "fixed";
			e.style.bottom 		= "0px";
			e.style.right 		= "10px";
		}
		
		e.style.width 		= "400px";
		e.style.height 		= "415px";
		e.style.zIndex		= "9999999";

	    var s = document.getElementsByTagName('body')[0].children[0]; 
	    s.parentNode.insertBefore(e, s);

	    e.innerHTML = '<iframe id="igcIframe" src="http://fraz.koding.io/widget?'+viewType+'='+channel_id+'" style="border: 0pt none ;'+ 
	                    'width: 400px;'+ 
	                    'height: 415px;" scrolling="no" allowtransparency="true"></iframe>';
	}
})();