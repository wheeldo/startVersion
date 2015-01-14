
(function( $ ) {
	
	
	
	  var methods = {
			  
			    init : function( SelObj , settings ) {
                                this.SelObj=SelObj;
                                this.settings=settings;
                                this.currslide=0;
                                this.allowSlide=true;
                                this.createFrame(SelObj , settings);
                                this.startSlide(SelObj , settings); 
                                if(settings.keys)
                                    this.setKeyboardEvents();
                                    
			    },
                            
                            getSlide : function() {
                                return this.currslide;
                            },
                            
                            removeSlider : function() {
                                var settings=this.settings;
                                var SelObj=this.SelObj;
                                if(SelObj)
                                    SelObj.html("");
                            },
                                    
                            setKeyboardEvents : function() {
                                var self=this;
                                document.onkeydown=function checkArrowKeys(e){
                                    var arrs= [], key= window.event? event.keyCode: e.keyCode;
                                    arrs[37]= 'left';
                                    arrs[38]= 'up';
                                    arrs[39]= 'right';
                                    arrs[40]= 'down';

                                    switch(arrs[key]){
                                        case "right":
                                            self.next_slide();
                                        break;
                                        case "left":
                                            self.prev_slide();
                                        break;
                                        case "up":
                                            location.hash = "#mainWrapper";
                                        break;
                                        case "down":
                                            location.hash = "#activity";
                                        break;
                                    }
                                }


                            },
                                                  
                            startSlide : function(SelObj , settings) {
                                var img_wrap=$(".slides_wrap .img_wrap");
                                var img_max_width=img_wrap.width()-6;
                                var img_max_height=img_wrap.height();
                                img_wrap.append('<img style="width:'+img_max_width+'px;max-height:'+img_max_height+'px" id="wheeldo_slide_img" />');
                                img_wrap.css("line-height",img_max_height+"px");
                                
                                $("#fadeBG").css({
                                    width:img_max_width+"px",
                                    height:img_max_height-2+"px",
                                    "margin-top":(img_max_height*0.02)+13+"px",
                                    "margin-left":(img_max_width*0.02)+14+"px",
                                });
                                var slides=settings.slides;
                                this.setSlide(0,true);
                            },
                                    
                            next_slide : function() {
                               var settings=this.settings;
                               var SelObj=this.SelObj;
                               var slides=settings.slides;
                               
                               if(slides[this.currslide+1]) {
                                   this.setSlide(this.currslide+1);
                               }   
                            },
                                    
                            next_end : function() {
                                var settings=this.settings;
                                var SelObj=this.SelObj;
                                var slides=settings.slides;
                                var end=slides.length-1;
                                this.setSlide(end);
                            },
                                    
                            prev_slide : function() {
                               var settings=this.settings;
                               var SelObj=this.SelObj;
                               var slides=settings.slides;
                               
                               if(this.currslide>0) {
                                   this.setSlide(this.currslide-1);
                               }
                            },
                                    
                            prev_end : function() {
                               var settings=this.settings;
                               var SelObj=this.SelObj;
                               var slides=settings.slides;
                               this.setSlide(0);
                            },
                                    
                            setSlide : function(slide,force) {
                               force = typeof force !== 'undefined' ? force : false;
                               if(!this.allowSlide && !force) {
                                   return;
                               }
                               var prev_slide=this.currslide;
                               var settings=this.settings;
                               var slides=settings.slides;
                               
                               if(slide<0) 
                                   slide=0;
                               if(slide>(slides.length-1)) 
                                   slide=slides.length-1;
                               
                               var t=new Date().getTime();
                               $("#wheeldo_slide_img").attr("src",slides[slide].src+"?t="+t);
                               this.currslide=slide;
                               this.updateCounter();
                               
                               if(prev_slide!=slide) {
                                   eval(settings.onSlideChange);
                               }
                            },        
                                    
                                    
                            updateCounter : function() {
                               var settings=this.settings;
                               var SelObj=this.SelObj;
                               var slides=settings.slides;
                               
                               $("#curr_slide").val(this.currslide+1);
                               $("#total_slides").html(slides.length);
                            }, 
                                    
                            set_q_mode : function() {
                               this.allowSlide=false;
                               $("#fadeBG").show();
                            },
                                    
                            exit_q_mode : function() {
                               this.allowSlide=true;
                               $("#fadeBG").hide();
                            },
                                    
                                    
                                    
                            createFrame : function(SelObj , settings) {
                                    var self=this;
                                    var width=SelObj.width();
                                    var slides_width=width-20;
                                    var slides_height=slides_width*0.7;
                                    var slides=settings.slides;
                                    var slides_wrap =
                                            '<div class="slides_wrap">\
                                                <div class="img_wrap">\
                                                </div>\
                                             </div>'
                                    
                                    SelObj.append(slides_wrap);
                                    $(".slides_wrap").css({
                                        width:slides_width+"px",
                                        height:slides_height+"px"
                                    });
                                    
                                    var action_bar =
                                            '<div class="action_bar">\
                                                <div class="pn">\
                                                    <button class="wheeldo_prev_end" type="button"></button>\
                                                    <button class="wheeldo_prev_slide" type="button"></button>\
                                                    <button class="wheeldo_next_slide" type="button"></button>\
                                                    <button class="wheeldo_next_end" type="button"></button>\
                                                </div>\
                                                <div class="slide_counter">\
                                                    <input value="34" type="text" id="curr_slide" /> / <span id="total_slides">24</span>\
                                                </div>\
                                             </div>'
                                    SelObj.append(action_bar);
                                    $(".action_bar").css({
                                        width:(width-10)+"px",
                                        
                                    });
                                    
                                    $("#curr_slide").unbind("click");
                                    $("#curr_slide").click(function(){
                                        $(this).select();
                                    });
                                    
                                    
                                    $("#curr_slide").unbind("keyup");
                                    $("#curr_slide").keyup(function(){
                                       var value=parseInt($(this).val());
                                       if(!value || value<0) {
                                           value=1;
                                       }
                                       if(value>slides.length) {
                                           value=slides.length;
                                       }
                                       
                                       if(!self.allowSlide) {
                                           value=self.currslide+1;
                                       }
                                       
                                       $(this).val(value);
                                       self.setSlide(value-1);
                                    });
                            }
	  };
	
	
	$.fn.wheeldo_slide = function(options) {
            
		
		var $this = $(this);
		var settings = $.extend( {
			  slides:[],
                          onSlideChange:"",
                          keys:true
		    }, options);
		
		
		
		methods.init($this,settings);
                
                var next = function() {
                    alert("here");
                }
	
	};
        
        
        $.fn.wheeldo_slide.next = function() {
            methods.next_slide();
        };
        
        $.fn.wheeldo_slide.next_end = function() {
            methods.next_end();
        };
        
        $.fn.wheeldo_slide.prev = function() {
            methods.prev_slide();
        };
        
        $.fn.wheeldo_slide.prev_end = function() {
            methods.prev_end();
        };
        
        $.fn.wheeldo_slide.set_q_mode = function() {
            methods.set_q_mode();
        };
        
        $.fn.wheeldo_slide.exit_q_mode = function() {
            methods.exit_q_mode();
        };
        
        $.fn.wheeldo_slide.removeSlider = function() {
            methods.removeSlider();
        };
        
        $.fn.wheeldo_slide.getSlide = function() {
            return methods.getSlide();
        };
        
        
        
        
        
        
        
        
       
})( jQuery );


