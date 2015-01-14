
(function( $ ) {
	
	
	
	  var methods = {
			  
			    init : function( Obj , settings ) { 
			    	//alert(settings.imgArary[0]);
                                this.currentSlide=1;
                                this.addFadeBG(Obj);
                                this.locateSlider(Obj,settings);
                                this.reLocate(Obj,settings);
                                this.setSlides(Obj,settings);
                                //this.nextSlide(Obj);
                                
			    },
			    
			    addFadeBG : function(Obj) {
			    	var self=this;
			    	if($('.wheeldoSliderFadeBG').length==0) {
                                    Obj.wrap('<div class="wheeldoSliderFadeBG"></div>');
                                }
                            },
                            
                            setStyle: function(Obj) {
                                var self=this;

                            },
                            
                            setSlides : function(Obj,settings) {
                              var objID=Obj.attr("id");
                              var left=$("html").outerWidth();
                              
                              
                              var start_left=(left/2)-400;
                              //start_left=0;
                              
                              Obj.find(".slide_screen").each(function(index){
                                  $(this).attr("id",objID+"_"+index);
                                  $(this).css({"left":start_left+(left*index)+"px"});
                              });
                            },
                            
                            nextSlide : function(Obj) {
                                // check if next slider exists: //
                                
                                var slides=Obj.find(".slide_screen").length;
                                var nextSlide=this.currentSlide+1;
                                if(nextSlide>slides) {
                                    return;
                                }
                                
                                //////////////////////////////////
                                
                                
                                
                                var left=$("html").outerWidth();
                                this.currentSlide=nextSlide;
                                
                                
                                 Obj.animate({

                                    left: '-='+left
 
                                    }, 1000, function() {
                                    // Animation complete.
                                    });
                                //Obj.css({"left":"-"+left+"px"});
                            },
                            
                            prevSlide : function(Obj) {
                                if(this.currentSlide==1)
                                    return;
                                
                                
                                var left=$("html").outerWidth();
                                this.currentSlide--;
                                
                                
                                 Obj.animate({

                                    left: '+='+left
 
                                    }, 1000, function() {
                                    // Animation complete.
                                    });
                                //Obj.css({"left":"-"+left+"px"});
                            },
                            
                            getScreen : function() {
                                return $("html").outerWidth()+"_"+$(window).height();
                            },
                            
                            showSlide: function(Obj) {
                                $(".wheeldoSliderFadeBG").show();
                                Obj.show();
                            },
                            
                            hideSlide: function(Obj) {
                                $(".wheeldoSliderFadeBG").hide();
                                Obj.hide();
                            },
                            
                            getMaxHeight : function(Obj) {
                                var maxHeight=0;
                              Obj.find(".slide_screen").each(function(index){
                                  if(maxHeight<$(this).outerHeight())
                                      maxHeight=$(this).outerHeight();
                              });
                                
                                return maxHeight;
                            },
                            
                            locateSlider : function(Obj,settings) {
//                                $(".wheeldoSliderFadeBG").html('<div class="wheeldoSliderWrap">'+Obj.html()+'</div>');
//                                Obj.html("");
                                settings.initialScreen=this.getScreen();
                                
                                var totalWidth=$("html").outerWidth();
                                var totalHeight=$(window).height();
                                /* left calculation */
                                var width=Obj.outerWidth();
                                var popLeft=(totalWidth/2)-(width/2);
                                popLeft=0;
                                //////////////////////

                                /* top calculation */
                                var height=this.getMaxHeight(Obj);
                                // decrease 10% //

                                totalHeight=totalHeight*0.8;
                                var popTop=(totalHeight/2)-(height/2);
                                if(popTop<5)
                                    popTop=5;
                                /////////////////////

                                Obj.css({"left":popLeft+"px","top":popTop+"px"})  ;
                            },
                            
                            reLocate : function(Obj,settings) {
//                                var self=this;
//                                console.log("relocate check - "+ getScreen());
//                                if(getScreen()!=settings.initialScreen) {
//                                    console.log("locate");
//                                    self.locateSlider(Obj,settings);
//                                }
//                                console.log(settings.timer);
//                                if(settings.timer) setTimeout(self.reLocate, 300, Obj,settings);
                            }
                            
                            
                        
                        
				
				
	  };
	
	
	$.fn.wheeldoSlider = function(options) {
		
		var $this = $(this);
		var settings = $.extend( {
                      initialScreen:"",
                      timer:true
		    }, options);
		
		
		
		methods.init($this,settings);
                
                this.getInfo = function () {
                        alert("asas");
                };
		
	
	};
        
        
        $.fn.next = function() {
            var $this = $(this);
            methods.nextSlide($this);
        }
        
        $.fn.prev = function() {
            var $this = $(this);
            methods.prevSlide($this);
        }
        
        $.fn.showSlide = function() {
            var $this = $(this);
            methods.showSlide($this);
        }
        
        $.fn.hideSlide = function() {
            var $this = $(this);
            methods.hideSlide($this);
        }
        
        
})( jQuery );