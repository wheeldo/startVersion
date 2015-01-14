
(function( $ ) {
	
	
	
	  var methods = {
			  
			    init : function( SelObj , settings) {
                                var int;
                                SelObj.keyup(function(){
                                    clearInterval(int);
                                    int=setInterval(function(){
                                        if(settings.callback) settings.callback();
                                        clearInterval(int);
                                    },settings.delay,int);
                                });
                                    
			    },
                            
                            execute : function() {
                                console.log(this.aaaa);
                            }
                         
	  };
	
	
	$.fn.wheeldo_autosave = function(options) {
            
		var $this = $(this);
		var settings = $.extend( {
			  callback:false,
                          delay:2000
		    }, options);
		
		
		
		methods.init($this,settings);

	
	};
        
     
       
})( jQuery );


