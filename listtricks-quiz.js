/**
 * jQuery Shuffle (http://mktgdept.com/jquery-shuffle)
 * A jQuery plugin for shuffling a set of elements
 *
 * v0.0.1 - 13 November 2009
 *
 * Copyright (c) 2009 Chad Smith (http://twitter.com/chadsmith)
 * Dual licensed under the MIT and GPL licenses.
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * Shuffle elements using: $(selector).shuffle() or $.shuffle(selector)
 *
 **/
 function activateButton(bt){
	 
 }
(function(d){d.fn.shuffle=function(c){c=[];return this.each(function(){c.push(d(this).clone(true))}).each(function(a,b){d(b).replaceWith(c[a=Math.floor(Math.random()*c.length)]);c.splice(a,1)})};d.shuffle=function(a){return d(a).shuffle()}})(jQuery);
(function( $ ) {
  $.fn.listtricksQuiz = function(options) {
	 options = $.extend($.fn.listtricksQuiz.defaults,options);
	var plugin = this;
	
	 return $(this).each(function(){//divs are sent here
		if(options.addClass)$(this).addClass(options.addClass);
		var context = $(this);
		$(this).data("score",0);
		//is there a better way to do this?
		$(this).data("outof",$(this).find("ol ol li").not("ol ol ol li").length );
	
		$(this).addClass("quiz").prepend('<div class="qscore"/>').append('<div class="feedback">Mouse over or touch to reveal answer options. Click or tap again to select.</div>');
		$(this).find("ol li:first").showQuestion(options,context);
	 });

  }

 	$.fn.showQuestion=function(options,context){
		var thisq = $(this);
		$(this).show().siblings().hide();
		
		  $(this).parents("div.quiz").children("div.feedback").before('<div id="buttons"></div>');
		 $("div.feedback").text("Mouse over or touch to reveal answer options. Click or tap again to select."); 
		  var myval = $(this).find("ol:first > li").length ;// -1 for questions to be worth the number of distractors avoided
		  $(this).data("value",myval);
		  $(this).find("ol:first > li:first").attr("data-value",1).siblings().attr("data-value",0);
		  $(this).find("ol:first > li").shuffle();
		 $(this).find("ol:first").hide().clone().appendTo("#buttons").show();
		 //$(this).find("ol:first > li").hide();
		 $("#buttons > ol > li").each(function(i){
			//var alphabet = "abcdefghijklmnopqrstuvwxyz";
			var myans = thisq.find("ol:first > li:eq("+i+")");
			$(this).html('<button>' + String.fromCharCode(i + 97) + '</button>').mouseenter(function(obj){
				obj.preventDefault();
			$(".active").removeClass("active");
					$(obj.target).children("button").addClass("active");//hover event is sending a different target than click!
					var start = i+1;
					thisq.children(".ans").remove();
					thisq.append('<ol class="ans" start="'+start+'"><li>'+myans.html() + "</li></ol>");
					context.children("div.feedback").html("Click to select this answer");
		}).click(function(obj){
				if(!$(obj.target).hasClass("active")){//touch
					//$(".active").removeClass("active");
					$(obj.target).addClass("active");
					var start = i+1;
					thisq.children(".ans").remove();
					thisq.append('<ol class="ans" start="'+start+'"><li>'+myans.html() + "</li></ol>");
					context.children("div.feedback").html("Tap again to select this answer");
					obj.preventDefault();
				}else{
					//$(obj.target).removeClass("active");
					context.children("div.feedback").html("");
				}
				var myresponse = myans.children("ol").html();
				
				var fbd=context.children("div.feedback");
				fbd.html(myresponse);
				
				if($(obj.target).hasClass("disabled")){
										  }else{
				if($(this).data("value")==0){
					if(myresponse==null)fbd.text(options.sorry);
					thisq.data("value",thisq.data("value") - 1) ;
					
				}else{
					var d = context;
					d.data("score", d.data("score") + thisq.data("value")) ;
					d.find(".qscore").text('Score: ' + d.data("score"));
					$(obj.target).siblings().addClass("disabled");
					if(myresponse==null)fbd.text(options.congrats);
					//alert(thisq.index() + "," + thisq.parent().children().length);
					if(thisq.index()==thisq.parent().children().length-1){//
						$("<li><button >Submit Score</button></li>").appendTo("#buttons ol").click(function(){
							var obj = new Object();
							obj.score = d.data("score");
							obj.outof = d.data("outof");
							
							options.sendScore.call(d,obj);													
							
						});								
					}else{
						$("<li><button >Next</button></li>").appendTo("#buttons ol").click(function(){
							$("#buttons").remove();																
							thisq.next("li").showQuestion(options,context);
						});//
					}
				}
				$(obj.target).addClass("disabled");
										  }
			/*	alert(thisq.data("value"));*/
			})
			
		})
	  };
  $.fn.listtricksQuiz.defaults = {
    color: '#fff47f',
	addClass:'',
    randomize:true,
	sendScore: null,
	feedback:"immediate",
	sorry:"Try again.",
	congrats:"Correct."
  };

  
})(jQuery);
